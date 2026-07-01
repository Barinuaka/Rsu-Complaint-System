<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\AnonymousVault;
use App\Models\ComplaintUpdate;
use App\Models\RoutingRule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ComplaintController extends Controller
{
    // Show the complaint submission form
    public function create()
    {
        return view('complaints.create');
    }

    // Handle complaint submission
    public function store(Request $request)
    {
        // Step 1: Validate inputs
        $request->validate([
            'complaint_title' => ['required', 'string', 'max:200'],
            'complaint_text'  => ['required', 'string', 'min:20'],
            'is_anonymous'    => ['nullable', 'boolean'],
            'evidence_file'   => ['nullable', 'file', 'max:5120', // 5MB max
                                  'mimes:pdf,jpg,jpeg,png,doc,docx'],
        ]);

        $isAnonymous = $request->boolean('is_anonymous');
        $user        = Auth::user();

        // Step 2: Handle file upload if provided
        $filePath = null;
        if ($request->hasFile('evidence_file')) {
            $filePath = $request->file('evidence_file')
                               ->store('evidence', 'public');
        }

        // Step 3: Call NLP microservice for classification
        $nlpResult = $this->callNlpService($request->complaint_text);

        // Step 4: Find the correct routing rule
        $routingRule = RoutingRule::where('category_id', $nlpResult['category_id'])
            ->where('campus_id', $user->campus_id)
            ->where('urgency_level', $nlpResult['urgency_level'])
            ->first();

        // Step 5: Find the handler user
        $handler = null;
        if ($routingRule) {
            $handler = User::where('role_id', $routingRule->primary_handler_role_id)
                          ->where('campus_id', $user->campus_id)
                          ->first();
        }

        // Step 6: Compute SLA deadline
        $slaDeadline = null;
        if ($routingRule) {
            $slaDeadline = Carbon::now()->addHours($routingRule->sla_hours);
        }

        // Step 7: Create the complaint record
        $complaint = Complaint::create([
            'submitter_id'       => $isAnonymous ? null : $user->id,
            'is_anonymous'       => $isAnonymous,
            'complaint_title'    => $request->complaint_title,
            'complaint_text'     => $request->complaint_text,
            'evidence_file_path' => $filePath,
            'category_id'        => $nlpResult['category_id'],
            'urgency_level'      => $nlpResult['urgency_level'],
            'nlp_confidence'     => $nlpResult['confidence'],
            'vader_compound_score' => $nlpResult['vader_score'],
            'campus_id'          => $user->campus_id,
            'assigned_to'        => $handler?->id,
            'current_status'     => $handler ? 'assigned' : 'submitted',
            'sla_deadline_at'    => $slaDeadline,
        ]);

        // Step 8: If anonymous, encrypt identity and store in vault
        if ($isAnonymous) {
            $identityData = json_encode([
                'user_id'       => $user->id,
                'full_name'     => $user->full_name,
                'email'         => $user->email,
                'matric_number' => $user->matric_number,
            ]);

            // AES-256-GCM encryption via Laravel's Crypt facade
            $encryptedIdentity = Crypt::encryptString($identityData);

            AnonymousVault::create([
                'complaint_id'       => $complaint->id,
                'encrypted_identity' => $encryptedIdentity,
                'encryption_key_ref' => 'APP_KEY_V1',
                'authentication_tag' => hash('sha256', $identityData),
                'vault_created_at'   => now(),
            ]);
        }

        // Step 9: Write first audit trail entry
        ComplaintUpdate::create([
            'complaint_id'    => $complaint->id,
            'updated_by'      => $isAnonymous ? null : $user->id,
            'action_type'     => 'submitted',
            'previous_status' => null,
            'new_status'      => $complaint->current_status,
            'update_note'     => 'Complaint submitted. NLP category: ' .
                                 $nlpResult['category_name'] .
                                 '. Urgency: ' . $nlpResult['urgency_level'],
            'updated_at'      => now(),
        ]);

        // Step 10: Redirect to confirmation page with tracking token
        return redirect()->route('complaints.confirmation',
                                 $complaint->tracking_token);
    }

    // Show confirmation page with tracking token
    public function confirmation($token)
    {
        $complaint = Complaint::where('tracking_token', $token)->firstOrFail();
        return view('complaints.confirmation', compact('complaint'));
    }

    // Public tracking page (no login required)
    public function track(Request $request)
    {
        $complaint = null;
        if ($request->filled('token')) {
            $complaint = Complaint::where('tracking_token', $request->token)
                                  ->with('category')
                                  ->first();
        }
        return view('complaints.track', compact('complaint'));
    }

    // Call the Python Flask NLP microservice
    private function callNlpService(string $text): array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->post('http://127.0.0.1:5000/classify', [
                    'text' => $text,
                ]);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            // NLP service unavailable — use fallback
        }

        // Fallback: manual classification if Flask is offline
        return $this->fallbackClassification($text);
    }

    // Fallback classification when NLP microservice is unavailable
    private function fallbackClassification(string $text): array
    {
        $text  = strtolower($text);
        $score = 0;

        // Simple keyword-based urgency detection
        $criticalWords = ['harassment', 'assault', 'threat', 'extortion',
                          'rape', 'violence', 'blackmail', 'emergency'];
        $highWords     = ['unfair', 'wrong', 'corrupt', 'failed', 'missing',
                          'urgent', 'immediate', 'serious'];

        foreach ($criticalWords as $word) {
            if (str_contains($text, $word)) $score = 3;
        }
        foreach ($highWords as $word) {
            if (str_contains($text, $word) && $score < 3) $score = 2;
        }

        $urgencyMap = [0 => 'low', 1 => 'medium', 2 => 'high', 3 => 'critical'];

        // Simple category detection
        $categoryId   = 5; // Default: General Guidance
        $categoryName = 'General Guidance';

        if (str_contains($text, 'grade') || str_contains($text, 'exam') ||
            str_contains($text, 'result') || str_contains($text, 'course')) {
            $categoryId = 1; $categoryName = 'Academic';
        } elseif (str_contains($text, 'harass') || str_contains($text, 'threat') ||
                  str_contains($text, 'extort') || str_contains($text, 'safe')) {
            $categoryId = 2; $categoryName = 'Welfare and Security';
        } elseif (str_contains($text, 'hostel') || str_contains($text, 'facility') ||
                  str_contains($text, 'toilet') || str_contains($text, 'light')) {
            $categoryId = 3; $categoryName = 'Infrastructure';
        } elseif (str_contains($text, 'fee') || str_contains($text, 'bursary') ||
                  str_contains($text, 'payment') || str_contains($text, 'scholarship')) {
            $categoryId = 4; $categoryName = 'Financial';
        }

        return [
            'category_id'   => $categoryId,
            'category_name' => $categoryName,
            'urgency_level' => $urgencyMap[$score],
            'confidence'    => 0.6,
            'vader_score'   => -0.3,
        ];
    }

    // Show full complaint detail for officers
    public function detail($id)
    {
        $complaint = Complaint::with([
            'category',
            'campus',
            'submitter',
            'assignedOfficer',
            'updates.officer',
        ])->findOrFail($id);

        // Load potential escalation targets
        $escalationOfficers = User::whereIn('role_id', [4, 5, 6])
            ->where('campus_id', $complaint->campus_id)
            ->get();

        return view('complaints.detail', compact('complaint', 'escalationOfficers'));
    }

    // Officer marks complaint as in_review
    public function updateStatus(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $previousStatus = $complaint->current_status;
        $complaint->update(['current_status' => 'in_review']);

        ComplaintUpdate::create([
            'complaint_id'    => $complaint->id,
            'updated_by'      => Auth::id(),
            'action_type'     => 'status_changed',
            'previous_status' => $previousStatus,
            'new_status'      => 'in_review',
            'update_note'     => $request->note ?? 'Officer marked complaint as under review.',
            'updated_at'      => now(),
        ]);

        return redirect()->route('complaints.detail', $id)
            ->with('success', 'Complaint marked as In Review.');
    }

    // Officer resolves the complaint
    public function resolve(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $request->validate([
            'resolution_note' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $previousStatus = $complaint->current_status;

        $complaint->update([
            'current_status'  => 'resolved',
            'resolution_note' => $request->resolution_note,
            'resolved_at'     => now(),
        ]);

        ComplaintUpdate::create([
            'complaint_id'    => $complaint->id,
            'updated_by'      => Auth::id(),
            'action_type'     => 'resolved',
            'previous_status' => $previousStatus,
            'new_status'      => 'resolved',
            'update_note'     => $request->resolution_note,
            'updated_at'      => now(),
        ]);

        return redirect()->route('complaints.detail', $id)
            ->with('success', 'Complaint has been resolved successfully.');
    }

    // Officer escalates complaint to higher authority
    public function escalate(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $request->validate([
            'escalation_note'   => ['required', 'string', 'min:10'],
            'escalate_to_user'  => ['required', 'exists:users,id'],
        ]);

        $previousStatus = $complaint->current_status;

        $complaint->update([
            'current_status'    => 'escalated',
            'assigned_to'       => $request->escalate_to_user,
            'escalation_count'  => $complaint->escalation_count + 1,
        ]);

        ComplaintUpdate::create([
            'complaint_id'    => $complaint->id,
            'updated_by'      => Auth::id(),
            'action_type'     => 'escalated',
            'previous_status' => $previousStatus,
            'new_status'      => 'escalated',
            'update_note'     => 'Escalated: ' . $request->escalation_note,
            'updated_at'      => now(),
        ]);

        return redirect()->route('complaints.detail', $id)
            ->with('success', 'Complaint escalated successfully.');
    }
}