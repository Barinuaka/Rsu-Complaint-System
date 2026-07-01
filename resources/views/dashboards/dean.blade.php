<x-app-layout>
    <div class="min-h-screen bg-slate-100">

        {{-- Header --}}
        <div class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">Dean Dashboard</h2>
                        <p class="text-sm text-slate-500 mt-0.5">
                            Escalated complaints requiring your attention
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-red-50 text-red-700 rounded-full border border-red-200">
                        <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        {{ $complaints->count() }} Escalated
                    </span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

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

            {{-- Escalated Complaints Queue --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h4 class="font-bold text-slate-700">Escalated Complaint Queue</h4>
                    <span class="text-xs text-slate-400">Sorted by SLA deadline</span>
                </div>

                @if($complaints->isEmpty())
                    <div class="text-center py-16">
                        <p class="text-slate-400 text-sm">No escalated complaints at this time.</p>
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
                                    <th class="px-6 py-3.5">SLA Deadline</th>
                                    <th class="px-6 py-3.5 text-center">Status</th>
                                    <th class="px-6 py-3.5 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($complaints as $complaint)
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
                                        <td class="px-6 py-4 text-slate-500">
                                            {{ $complaint->sla_deadline_at ? $complaint->sla_deadline_at->format('d M, H:i') : '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-xs font-semibold capitalize px-2 py-1 rounded bg-red-50 text-red-700">
                                                {{ ucfirst(str_replace('_', ' ', $complaint->current_status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('complaints.detail', $complaint->id) }}"
                                               class="text-xs font-semibold text-indigo-600 hover:underline">
                                                View &amp; Act
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