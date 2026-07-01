<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Campus;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Admin dashboard — system overview with KPI stats
     * and recent complaints across all campuses.
     */
    public function dashboard()
    {
        $totalUsers      = User::count();
        $totalComplaints = Complaint::count();
        $resolvedCount   = Complaint::where('current_status', 'resolved')->count();
        $pendingCount    = Complaint::whereNotIn('current_status', ['resolved', 'closed'])->count();
        $escalatedCount  = Complaint::where('current_status', 'escalated')->count();

        $recentComplaints = Complaint::with(['category', 'campus', 'assignedOfficer'])
            ->latest()
            ->take(10)
            ->get();

        return view('dashboards.admin', compact(
            'totalUsers',
            'totalComplaints',
            'resolvedCount',
            'pendingCount',
            'escalatedCount',
            'recentComplaints'
        ));
    }

    /**
     * List all registered users with their role and campus.
     */
    public function users()
    {
        $users = User::with(['role', 'campus'])
            ->orderBy('role_id')
            ->orderBy('full_name')
            ->get();

        return view('admin.users', compact('users'));
    }

    /**
     * Show the create staff account form.
     * Students are excluded — they self-register publicly.
     */
    public function createUser()
    {
        $roles    = Role::where('role_name', '!=', 'Student')->get();
        $campuses = Campus::all();

        return view('admin.create-user', compact('roles', 'campuses'));
    }

    /**
     * Store the new staff account in the database.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'full_name'    => ['required', 'string', 'max:150'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'role_id'      => ['required', 'exists:roles,id'],
            'campus_id'    => ['required', 'exists:campuses,id'],
            'department'   => ['nullable', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        User::create([
            'full_name'      => $request->full_name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role_id'        => $request->role_id,
            'campus_id'      => $request->campus_id,
            'department'     => $request->department,
            'phone_number'   => $request->phone_number,
            'account_status' => 'active',
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'Staff account created successfully.');
    }

    /**
     * Toggle a user's account between active and suspended.
     * Admin cannot suspend their own account.
     */
    public function toggleStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot suspend your own account.');
        }

        $newStatus = $user->account_status === 'active' ? 'suspended' : 'active';
        $user->update(['account_status' => $newStatus]);

        return redirect()->route('admin.users')
            ->with('success', "Account {$newStatus} successfully.");
    }
}