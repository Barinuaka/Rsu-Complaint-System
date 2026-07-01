<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * Only Students can self-register.
     * All staff accounts are created by the System Administrator.
     */
    public function create(): View
    {
        $campuses = \App\Models\Campus::all();
        $roles    = \App\Models\Role::where('role_name', 'Student')->get();

        return view('auth.register', compact('campuses', 'roles'));
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name'     => ['required', 'string', 'max:150'],
            'email'         => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'      => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id'       => ['required', 'exists:roles,id'],
            'campus_id'     => ['required', 'exists:campuses,id'],
            'matric_number' => ['nullable', 'string', 'max:20', 'unique:users,matric_number'],
            'phone_number'  => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'full_name'     => $request->full_name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role_id'       => $request->role_id,
            'campus_id'     => $request->campus_id,
            'matric_number' => $request->matric_number,
            'phone_number'  => $request->phone_number,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}