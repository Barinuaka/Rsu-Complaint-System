<x-app-layout>
    <div class="min-h-screen bg-slate-100 text-slate-900 antialiased font-sans">

        {{-- Header --}}
        <div class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                            Course Adviser Dashboard
                        </h2>
                        <p class="text-sm text-slate-500 mt-0.5 font-medium">
                            Rivers State University — Student Complaint Queue
                        </p>
                    </div>
                    <div class="flex items-center self-start md:self-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-emerald-50 text-emerald-700 rounded-full border border-emerald-200 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live System Gateway
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Assigned to Me</p>
                            <h3 class="text-4xl font-black text-slate-800 mt-2">{{ $complaints->count() }}</h3>
                        </div>
                        <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">SLA Target</p>
                            <h3 class="text-lg font-extrabold text-indigo-700 mt-3">48-Hour Resolution</h3>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Authorized Officer</p>
                            <h3 class="text-sm font-bold text-slate-700 mt-4 truncate max-w-[190px]">
                                {{ Auth::user()->full_name ?? 'Course Adviser' }}
                            </h3>
                            <p class="text-xs text-slate-400 mt-1">
                                {{ Auth::user()->campus->campus_name ?? '' }}
                            </p>
                        </div>
                        <div class="p-3 bg-slate-100 rounded-xl text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Complaint Queue Table --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h4 class="font-bold text-slate-700 text-base">Student Complaints Assigned to You</h4>
                    <span class="text-xs font-bold bg-indigo-600 text-white px-2.5 py-1 rounded-md shadow-sm">
                        Action Required
                    </span>
                </div>

                @if($complaints->isEmpty())
                    <div class="text-center py-16 px-4 bg-white">
                        <span class="text-4xl">🍃</span>
                        <h5 class="text-base font-bold text-slate-700 mt-3">Your queue is clear</h5>
                        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">
                            No student complaints are currently assigned to you.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-xs text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                                    <th class="px-6 py-3.5">Token</th>
                                    <th class="px-6 py-3.5">Complaint Summary</th>
                                    <th class="px-6 py-3.5 text-center">Category</th>
                                    <th class="px-6 py-3.5 text-center">Priority</th>
                                    <th class="px-6 py-3.5 text-center">Status</th>
                                    <th class="px-6 py-3.5 text-right">SLA Deadline</th>
                                    <th class="px-6 py-3.5 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($complaints as $complaint)
                                    <tr class="hover:bg-slate-50/60 transition duration-150">

                                        {{-- Token --}}
                                        <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-bold text-indigo-600">
                                            <a href="{{ route('complaints.detail', $complaint->id) }}"
                                               class="bg-indigo-50 border border-indigo-100 px-2 py-1 rounded shadow-inner hover:bg-indigo-100 transition">
                                                {{ Str::limit($complaint->tracking_token, 8, '') }}
                                            </a>
                                        </td>

                                        {{-- Title --}}
                                        <td class="px-6 py-4">
                                            <a href="{{ route('complaints.detail', $complaint->id) }}"
                                               class="text-sm font-semibold text-slate-800 hover:text-indigo-600 transition">
                                                {{ Str::limit($complaint->complaint_title, 50) }}
                                            </a>
                                        </td>

                                        {{-- Category --}}
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-xs font-medium text-slate-600 bg-slate-100 px-2 py-1 rounded">
                                                {{ $complaint->category->category_name ?? 'Uncategorised' }}
                                            </span>
                                        </td>

                                        {{-- Priority --}}
                                        <td class="px-6 py-4 text-center">
                                            @if(strtolower($complaint->urgency_level) === 'critical')
                                                <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded bg-rose-100 text-rose-700 border border-rose-200 animate-pulse">Critical</span>
                                            @elseif(strtolower($complaint->urgency_level) === 'high')
                                                <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded bg-orange-100 text-orange-700 border border-orange-200">High</span>
                                            @elseif(strtolower($complaint->urgency_level) === 'medium')
                                                <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded bg-yellow-100 text-yellow-700 border border-yellow-200">Medium</span>
                                            @else
                                                <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded bg-green-100 text-green-700 border border-green-200">Low</span>
                                            @endif
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-xs font-semibold capitalize bg-blue-50 text-blue-700 px-2 py-1 rounded border border-blue-100">
                                                {{ ucfirst(str_replace('_', ' ', $complaint->current_status)) }}
                                            </span>
                                        </td>

                                        {{-- SLA Deadline --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold text-slate-600">
                                            <span class="bg-slate-100 border border-slate-200 px-2 py-1 rounded text-slate-700 shadow-sm">
                                                @if($complaint->sla_deadline_at)
                                                    ⏱️ {{ \Carbon\Carbon::parse($complaint->sla_deadline_at)->diffForHumans() }}
                                                @else
                                                    No SLA set
                                                @endif
                                            </span>
                                        </td>

                                        {{-- Action Button --}}
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('complaints.detail', $complaint->id) }}"
                                               class="inline-block bg-indigo-600 text-white text-xs font-semibold px-3 py-1.5 rounded hover:bg-indigo-700 transition">
                                                View & Act
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