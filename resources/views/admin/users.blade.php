<x-app-layout>
    <div class="min-h-screen bg-slate-100">

        {{-- Header --}}
        <div class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800">User Management</h2>
                        <p class="text-sm text-slate-500 mt-0.5">
                            All system accounts — Rivers State University
                        </p>
                    </div>
                    <a href="{{ route('admin.users.create') }}"
                       class="bg-indigo-600 text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                        + Create Staff Account
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            {{-- Success / Error messages --}}
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm font-medium">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm font-medium">
                    ✗ {{ session('error') }}
                </div>
            @endif

            {{-- Stats row --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                @php
                    $roles = ['Student', 'Lecturer', 'Course Adviser', 'Head of Department', 'Dean', 'System Administrator'];
                    $counts = $users->groupBy(fn($u) => $u->role->role_name ?? 'Unknown');
                @endphp
                <div class="bg-white rounded-xl border border-slate-200 p-4 text-center">
                    <p class="text-3xl font-black text-slate-800">{{ $users->count() }}</p>
                    <p class="text-xs text-slate-400 font-semibold uppercase mt-1">Total Users</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-4 text-center">
                    <p class="text-3xl font-black text-indigo-600">{{ $counts->get('Student', collect())->count() }}</p>
                    <p class="text-xs text-slate-400 font-semibold uppercase mt-1">Students</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-4 text-center">
                    <p class="text-3xl font-black text-emerald-600">{{ $users->where('account_status', 'active')->count() }}</p>
                    <p class="text-xs text-slate-400 font-semibold uppercase mt-1">Active</p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-4 text-center">
                    <p class="text-3xl font-black text-red-500">{{ $users->where('account_status', 'suspended')->count() }}</p>
                    <p class="text-xs text-slate-400 font-semibold uppercase mt-1">Suspended</p>
                </div>
            </div>

            {{-- Users table --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50">
                    <h4 class="font-bold text-slate-700">All Registered Users</h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-xs text-slate-500 uppercase font-bold border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-3.5">Full Name</th>
                                <th class="px-6 py-3.5">Email</th>
                                <th class="px-6 py-3.5">Role</th>
                                <th class="px-6 py-3.5">Campus</th>
                                <th class="px-6 py-3.5">Department</th>
                                <th class="px-6 py-3.5 text-center">Status</th>
                                <th class="px-6 py-3.5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($users as $user)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td class="px-6 py-4 font-semibold text-slate-800">
                                        {{ $user->full_name }}
                                        @if($user->id === auth()->id())
                                            <span class="text-xs text-indigo-400 ml-1">(you)</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">{{ $user->email }}</td>
                                    <td class="px-6 py-4">
                                        <span class="text-xs font-semibold px-2 py-1 rounded bg-slate-100 text-slate-700">
                                            {{ $user->role->role_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $user->campus->campus_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">
                                        {{ $user->department ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($user->account_status === 'active')
                                            <span class="text-xs font-bold px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">
                                                Active
                                            </span>
                                        @else
                                            <span class="text-xs font-bold px-2 py-1 rounded-full bg-red-100 text-red-700 border border-red-200">
                                                Suspended
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($user->id !== auth()->id())
                                            <form method="POST"
                                                  action="{{ route('admin.users.toggle', $user->id) }}">
                                                @csrf
                                                <button type="submit"
                                                    class="text-xs font-semibold px-3 py-1.5 rounded transition
                                                    {{ $user->account_status === 'active'
                                                        ? 'bg-red-50 text-red-600 border border-red-200 hover:bg-red-100'
                                                        : 'bg-emerald-50 text-emerald-600 border border-emerald-200 hover:bg-emerald-100' }}">
                                                    {{ $user->account_status === 'active' ? 'Suspend' : 'Activate' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs text-slate-300">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('admin.dashboard') }}"
                   class="text-sm text-indigo-600 hover:underline">
                    ← Back to Admin Dashboard
                </a>
            </div>

        </div>
    </div>
</x-app-layout>