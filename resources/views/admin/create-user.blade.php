<x-app-layout>
    <div class="min-h-screen bg-slate-100">

        {{-- Header --}}
        <div class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">Create Staff Account</h2>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Add a new HOD, Course Adviser, Dean, Lecturer, or Admin account
                    </p>
                </div>
            </div>
        </div>

        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                    <p class="font-semibold mb-1">Please fix the following:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Full Name</label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                               required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                            <input type="password" name="password"
                                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                   required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Role</label>
                            <select name="role_id"
                                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    required>
                                <option value="">Select role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Campus</label>
                            <select name="campus_id"
                                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    required>
                                <option value="">Select campus</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                                        {{ $campus->campus_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Department (optional)</label>
                        <input type="text" name="department" value="{{ old('department') }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Phone Number (optional)</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <a href="{{ route('admin.users') }}" class="text-sm text-slate-500 hover:underline">
                            ← Cancel
                        </a>
                        <button type="submit"
                                class="bg-indigo-600 text-white text-sm font-semibold px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>