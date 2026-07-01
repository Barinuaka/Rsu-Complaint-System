<x-app-layout>
    <div class="min-h-screen bg-slate-100">

        {{-- Header --}}
        <div class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                            System Administrator Dashboard
                        </h2>
                        <p class="text-sm text-slate-500 mt-0.5">
                            Rivers State University — RSU Complaint System Control Panel
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Live System
                    </span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            {{-- KPI Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm text-center">
                    <p class="text-3xl font-black text-slate-800">{{ $totalUsers }}</p>
                    <p class="text-xs text-slate-400 font-bold uppercase mt-1">Total Users</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm text-center">
                    <p class="text-3xl font-black text-indigo-600">{{ $totalComplaints }}</p>
                    <p class="text-xs text-slate-400 font-bold uppercase mt-1">Total Complaints</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm text-center">
                    <p class="text-3xl font-black text-amber-500">{{ $pendingCount }}</p>
                    <p class="text-xs text-slate-400 font-bold uppercase mt-1">Pending</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm text-center">
                    <p class="text-3xl font-black text-red-500">{{ $escalatedCount }}</p>
                    <p class="text-xs text-slate-400 font-bold uppercase mt-1">Escalated</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm text-center">
                    <p class="text-3xl font-black text-emerald-600">{{ $resolvedCount }}</p>
                    <p class="text-xs text-slate-400 font-bold uppercase mt-1">Resolved</p>
                </div>
            </div>

            {{-- Quick Action Links --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <a href="{{ route('admin.users') }}"
                   class="bg-indigo-600 text-white p-5 rounded-xl hover:bg-indigo-700 transition flex items-center gap-4">
                    <div class="p-3 bg-indigo-500 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-base">Manage Users</p>
                        <p class="text-xs text-indigo-200 mt-0.5">View, create and suspend accounts</p>
                    </div>
                </a>
                <a href="{{ route('admin.users.create') }}"
                   class="bg-white border border-slate-200 text-slate-700 p-5 rounded-xl hover:shadow-md transition flex items-center gap-4">
                    <div class="p-3 bg-slate-100 rounded-xl text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-base">Create Staff Account</p>
                        <p class="text-xs text-slate-400 mt-0.5">Add HOD, Adviser, Dean, DPO</p>
                    </div>
                </a>
                <a href="{{ route('complaints.track') }}"
                   class="bg-white border border-slate-200 text-slate-700 p-5 rounded-xl hover:shadow-md transition flex items-center gap-4">
                    <div class="p-3 bg-slate-100 rounded-xl text-slate-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-base">Track Any Complaint</p>
                        <p class="text-xs text-slate-400 mt-0.5">Look up any complaint by token</p>
                    </div>
                </a>
            </div>

            {{-- Recent Complaints Table --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h4 class="font-bold text-slate-700">Recent Complaints — All Campuses</h4>
                    <span class="text-xs text-slate-400">Showing latest 10</span>
                </div>
                @if($recentComplaints->isEmpty())
                    <div class="text-center py-12">
                        <p class="text-slate-400 text-sm">No complaints submitted yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-50 text-xs text-slate-500 uppercase font-bold border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-3.5">Title</th>
                                    <th class="px-6 py-3.5">Category</th>
                                    <th class="px-6 py-3.5">Campus</th>
                                    <th class="px-6 py-3.5 text-center">Urgency</th>
                                    <th class="px-6 py-3.5 text-center">Status</th>
                                    <th class="px-6 py-3.5 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($recentComplaints as $complaint)
                                    <tr class="hover:bg-slate-50/60 transition">
                                        <td class="px-6 py-4 font-medium text-slate-800">
                                            {{ Str::limit($complaint->complaint_title, 40) }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-500">
                                            {{ $complaint->category->category_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-slate-500">
                                            {{ $complaint->campus->campus_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($complaint->urgency_level === 'critical')
                                                <span class="text-[10px] font-black uppercase px-2 py-1 rounded bg-rose-100 text-rose-700 animate-pulse">Critical</span>
                                            @elseif($complaint->urgency_level === 'high')
                                                <span class="text-[10px] font-black uppercase px-2 py-1 rounded bg-orange-100 text-orange-700">High</span>
                                            @elseif($complaint->urgency_level === 'medium')
                                                <span class="text-[10px] font-black uppercase px-2 py-1 rounded bg-yellow-100 text-yellow-700">Medium</span>
                                            @else
                                                <span class="text-[10px] font-black uppercase px-2 py-1 rounded bg-green-100 text-green-700">Low</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-xs font-semibold capitalize px-2 py-1 rounded bg-blue-50 text-blue-700">
                                                {{ ucfirst(str_replace('_', ' ', $complaint->current_status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('complaints.detail', $complaint->id) }}"
                                               class="text-xs font-semibold text-indigo-600 hover:underline">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>