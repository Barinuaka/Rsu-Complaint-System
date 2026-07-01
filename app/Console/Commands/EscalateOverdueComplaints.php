<?php

namespace App\Console\Commands;

use App\Models\Complaint;
use App\Models\ComplaintUpdate;
use App\Models\User;
use Illuminate\Console\Command;

class EscalateOverdueComplaints extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'complaints:escalate-overdue';

    /**
     * The console command description.
     */
    protected $description = 'Auto-escalate complaints whose SLA deadline has passed without resolution.';

    /**
     * Escalation ladder — role IDs in ascending order of authority.
     * Matches the escalation target pool already used in ComplaintController@detail().
     */
    protected array $escalationLadder = [4, 5, 6]; // HOD -> Dean -> next tier

    /**
     * Maximum number of automatic escalations before we stop
     * reassigning and simply notify the VC office instead.
     */
    protected int $maxEscalations = 3;

    public function handle()
    {
        $overdueComplaints = Complaint::where('sla_deadline_at', '<', now())
            ->whereNotIn('current_status', ['resolved', 'closed'])
            ->get();

        if ($overdueComplaints->isEmpty()) {
            $this->info('No overdue complaints found. Nothing to escalate.');
            return self::SUCCESS;
        }

        $this->info("Found {$overdueComplaints->count()} overdue complaint(s). Processing...");

        foreach ($overdueComplaints as $complaint) {
            $this->processComplaint($complaint);
        }

        $this->info('SLA escalation sweep complete.');
        return self::SUCCESS;
    }

    /**
     * Decide whether a single overdue complaint gets escalated
     * to the next handler, or has hit the cap and needs VC notification.
     *
     * NOTE: action_type is a fixed ENUM in the database
     * (submitted, status_changed, resolved, escalated).
     * We reuse those existing values and put the "auto" distinction
     * into update_note instead of inventing new ENUM values.
     */
    protected function processComplaint(Complaint $complaint): void
    {
        $previousStatus = $complaint->current_status;

        // Cap reached — notify VC office instead of escalating further.
        if ($complaint->escalation_count >= $this->maxEscalations) {
            ComplaintUpdate::create([
                'complaint_id'    => $complaint->id,
                'updated_by'      => null,
                'action_type'     => 'status_changed',
                'previous_status' => $previousStatus,
                'new_status'      => $previousStatus,
                'update_note'     => 'AUTO: SLA breached repeatedly (escalation cap of '
                                     . $this->maxEscalations
                                     . ' reached). VC office has been notified for direct intervention.',
                'updated_at'      => now(),
            ]);

            $this->warn("Complaint #{$complaint->id}: cap reached, VC notified (not reassigned).");
            return;
        }

        // Find the current handler's role tier, then bump to the next one up.
        $currentHandler   = $complaint->assigned_to ? User::find($complaint->assigned_to) : null;
        $currentRoleId    = $currentHandler?->role_id ?? 0;

        $nextRoleId = collect($this->escalationLadder)
            ->first(fn ($roleId) => $roleId > $currentRoleId);

        if (! $nextRoleId) {
            // Already at the top of the ladder — same as hitting the cap.
            ComplaintUpdate::create([
                'complaint_id'    => $complaint->id,
                'updated_by'      => null,
                'action_type'     => 'status_changed',
                'previous_status' => $previousStatus,
                'new_status'      => $previousStatus,
                'update_note'     => 'AUTO: SLA breached and no further escalation tier available. VC office notified.',
                'updated_at'      => now(),
            ]);

            $this->warn("Complaint #{$complaint->id}: no higher tier available, VC notified.");
            return;
        }

        $nextHandler = User::where('role_id', $nextRoleId)
            ->where('campus_id', $complaint->campus_id)
            ->first();

        if (! $nextHandler) {
            // No one at that role/campus — still notify VC so it isn't silently stuck.
            ComplaintUpdate::create([
                'complaint_id'    => $complaint->id,
                'updated_by'      => null,
                'action_type'     => 'status_changed',
                'previous_status' => $previousStatus,
                'new_status'      => $previousStatus,
                'update_note'     => 'AUTO: SLA breached but no handler found at the next escalation tier for this campus. VC office notified.',
                'updated_at'      => now(),
            ]);

            $this->warn("Complaint #{$complaint->id}: no handler found at role {$nextRoleId}, VC notified.");
            return;
        }

        // Reassign and escalate — mirrors ComplaintController@escalate()
        $complaint->update([
            'current_status'   => 'escalated',
            'assigned_to'      => $nextHandler->id,
            'escalation_count' => $complaint->escalation_count + 1,
        ]);

        ComplaintUpdate::create([
            'complaint_id'    => $complaint->id,
            'updated_by'      => null,
            'action_type'     => 'escalated',
            'previous_status' => $previousStatus,
            'new_status'      => 'escalated',
            'update_note'     => 'AUTO: SLA deadline passed without resolution. Reassigned to '
                                 . $nextHandler->full_name . '.',
            'updated_at'      => now(),
        ]);

        $this->info("Complaint #{$complaint->id}: auto-escalated to {$nextHandler->full_name}.");
    }
}