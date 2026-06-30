<x-app-layout>
    <div class="min-h-screen bg-slate-100 text-slate-900 antialiased font-sans">
        
        <div class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-800 tracking-tight">
                            Departmental Administrative Dashboard
                        </h2>
                        <p class="text-sm text-slate-500 mt-0.5 font-medium">
                            Rivers State University — Head of Department Management Queue
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
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Queue Cases</p>
                            <h3 class="text-4xl font-black text-slate-800 mt-2">{{ $complaints->count() }}</h3>
                        </div>
                        <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">SLA Target Standard</p>
                            <h3 class="text-lg font-extrabold text-indigo-700 mt-3">24-Hour Resolution</h3>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition duration-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Authorized Officer</p>
                            <h3 class="text-sm font-bold text-slate-700 mt-4 truncate max-w-[190px]">
                                {{ Auth::user()->full_name ?? 'Department Head' }}
                            </h3>
                        </div>
                        <div class="p-3 bg-slate-100 rounded-xl text-slate-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h4 class="font-bold text-slate-700 text-base">Incoming Student Grievances</h4>
                    <span class="text-xs font-bold bg-indigo-600 text-white px-2.5 py-1 rounded-md shadow-sm">Action Required</span>
                </div>

                @if($complaints->isEmpty())
                    <div class="text-center py-16 px-4 bg-white">
                        <span class="text-4xl">🍃</span>
                        <h5 class="text-base font-bold text-slate-700 mt-3">The administrative queue is empty</h5>
                        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">No outstanding student complaints require evaluation at this moment.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-xs text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                                    <th class="px-6 py-3.5 font-semibold">Token</th>
                                    <th class="px-6 py-3.5 font-semibold">Complaint Summary</th>
                                    <th class="px-6 py-3.5 font-semibold text-center">Priority</th>
                                    <th class="px-6 py-3.5 font-semibold text-right">Time Left</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($complaints as $complaint)
                                    <tr class="hover:bg-slate-50/60 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap font-mono text-xs font-bold text-indigo-600">
                                            <span class="bg-indigo-50 border border-indigo-100 px-2 py-1 rounded shadow-inner">
                                                {{ Str::limit($complaint->tracking_token, 8, '') }}
                                            </span>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-slate-800">
                                                    {{ $complaint->category->name ?? 'Academic Evaluation' }}
                                                </span>
                                                <span class="text-xs text-slate-400 font-normal mt-0.5">
                                                    {{ Str::limit($complaint->complaint_title, 45) }}
                                                </span>
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if(strtolower($complaint->urgency_level) === 'critical')
                                                <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider rounded bg-rose-100 text-rose-700 border border-rose-200 animate-pulse">Critical</span>
                                            @elseif(strtolower($complaint->urgency_level) === 'high')
                                                <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider rounded bg-orange-100 text-orange-700 border border-orange-200">High</span>
                                            @else
                                                <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider rounded bg-amber-100 text-amber-700 border border-amber-200">Normal</span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-slate-600 text-xs">
                                            <span class="bg-slate-100 border border-slate-200 px-2 py-1 rounded text-slate-700 shadow-sm">
                                                ⏱️ {{ \Carbon\Carbon::parse($complaint->sla_deadline_at)->diffForHumans() }}
                                            </span>
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