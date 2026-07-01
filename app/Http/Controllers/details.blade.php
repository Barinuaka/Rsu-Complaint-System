<x-app-layout>
    <div class="min-h-screen bg-slate-100">

        {{-- Header --}}
        <div class="bg-white border-b border-slate-200 shadow-sm">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">Complaint Detail</h2>
                        <p class="text-xs text-slate-400 font-mono mt-0.5">
                            Token: {{ $complaint->tracking_token }}
                        </p>
                    </div>
                    <a href="{{ url()->previous() }}"
                       class="text-sm text-indigo-600 hover:underline">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

            {{-- Success message --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm font-medium">
                    ✓ {{ session('success') }}
                </div>
            @endif

            {{-- Complaint Overview Card --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <div class="flex items-start justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-800">
                        {{ $complaint->complaint_title }}
                    </h3>
                    <span class="text-xs font-bold px-3 py-1 rounded-full
                        {{ $complaint->current_status === 'resolved' ? 'bg-green-100 text-green-700' : '' }}
                        {{ $complaint->current_status === 'escalated' ? 'bg-red-100 text-red-700' : '' }}
                        {{ $complaint->current_status === 'in_review' ? 'bg-blue-100 text-blue-700' : '' }}
                        {{ $complaint->current_status === 'assigned' ? 'bg-indigo-100 text-indigo-700' : '' }}
                        {{ $complaint->current_status === 'submitted' ? 'bg-gray-100 text-gray-700' : '' }}">
                        {{ ucfirst(str_replace('_', ' ', $complaint->current_status)) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm">
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase">Category</p>
                        <p class="font-medium mt-1">{{ $complaint->category->category_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase">Urgency</p>
                        <p class="font-bold mt-1
                            {{ $complaint->urgency_level === 'critical' ? 'text-red-600' : '' }}
                            {{ $complaint->urgency_level === 'high' ? 'text-orange-600' : '' }}
                            {{ $complaint->urgency_level === 'medium' ? 'text-yellow-600' : '' }}
                            {{ $complaint->urgency_level === 'low' ? 'text-green-600' : '' }}">
                            {{ ucfirst($complaint->urgency_level) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase">Campus</p>
                        <p class="font-medium mt-1">{{ $complaint->campus->campus_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase">SLA Deadline</p>
                        <p class="font-medium mt-1">
                            @if($complaint->sla_deadline_at)
                                {{ \Carbon\Carbon::parse($complaint->sla_deadline_at)->diffForHumans() }}
                            @else
                                Not set
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase">Submitted By</p>
                        <p class="font-medium mt-1">
                            @if($complaint->is_anonymous)
                                <span class="italic text-slate-400">Anonymous</span>
                            @else
                                {{ $complaint->submitter->full_name ?? 'Unknown' }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase">Submitted At</p>
                        <p class="font-medium mt-1">
                            {{ $complaint->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase">NLP Confidence</p>
                        <p class="font-medium mt-1">
                            {{ $complaint->nlp_confidence ? round($complaint->nlp_confidence * 100, 1) . '%' : 'N/A' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-semibold uppercase">Escalations</p>
                        <p class="font-medium mt-1">{{ $complaint->escalation_count }}</p>
                    </div>
                </div>

                {{-- Full complaint text --}}
                <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                    <p class="text-xs text-slate-400 font-semibold uppercase mb-2">Full Complaint</p>
                    <p class="text-sm text-slate-700 leading-relaxed">
                        {{ $complaint->complaint_text }}
                    </p>
                </div>

                {{-- Evidence file --}}
                @if($complaint->evidence_file_path)
                    <div class="mt-4">
                        <p class="text-xs text-slate-400 font-semibold uppercase mb-1">Evidence</p>
                        <a href="{{ Storage::url($complaint->evidence_file_path) }}"
                           target="_blank"
                           class="text-sm text-indigo-600 hover:underline">
                            View Attached Evidence File
                        </a>
                    </div>
                @endif

                {{-- Resolution note (if resolved) --}}
                @if($complaint->resolution_note)
                    <div class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-xs text-green-700 font-semibold uppercase mb-1">Resolution Note</p>
                        <p class="text-sm text-green-800">{{ $complaint->resolution_note }}</p>
                        <p class="text-xs text-green-500 mt-1">
                            Resolved: {{ \Carbon\Carbon::parse($complaint->resolved_at)->format('d M Y, H:i') }}
                        </p>
                    </div>
                @endif
            </div>

            {{-- Action Panel (only show if complaint is not resolved or closed) --}}
            @if(!in_array($complaint->current_status, ['resolved', 'closed']))
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h4 class="font-bold text-slate-700 mb-4">Actions</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        {{-- Mark In Review --}}
                        @if($complaint->current_status !== 'in_review')
                            <form method="POST"
                                  action="{{ route('complaints.updateStatus', $complaint->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="text-xs text-slate-500 font-semibold uppercase">Add Note (Optional)</label>
                                    <textarea name="note" rows="2"
                                        class="mt-1 block w-full border-slate-300 rounded text-sm"
                                        placeholder="Note about this action..."></textarea>
                                </div>
                                <button type="submit"
                                    class="w-full bg-blue-600 text-white text-sm font-semibold py-2 px-4 rounded hover:bg-blue-700 transition">
                                    Mark as In Review
                                </button>
                            </form>
                        @endif

                        {{-- Resolve --}}
                        <form method="POST"
                              action="{{ route('complaints.resolve', $complaint->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="text-xs text-slate-500 font-semibold uppercase">Resolution Note <span class="text-red-500">*</span></label>
                                <textarea name="resolution_note" rows="2"
                                    class="mt-1 block w-full border-slate-300 rounded text-sm"
                                    placeholder="Describe how this was resolved..."
                                    required></textarea>
                                @error('resolution_note')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                class="w-full bg-green-600 text-white text-sm font-semibold py-2 px-4 rounded hover:bg-green-700 transition">
                                Mark as Resolved
                            </button>
                        </form>

                        {{-- Escalate --}}
                        <form method="POST"
                              action="{{ route('complaints.escalate', $complaint->id) }}">
                            @csrf
                            <div class="mb-2">
                                <label class="text-xs text-slate-500 font-semibold uppercase">Escalate To</label>
                                <select name="escalate_to_user"
                                    class="mt-1 block w-full border-slate-300 rounded text-sm"
                                    required>
                                    <option value="">-- Select Officer --</option>
                                    @foreach($escalationOfficers as $officer)
                                        <option value="{{ $officer->id }}">
                                            {{ $officer->full_name }}
                                            ({{ $officer->role->role_name ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="text-xs text-slate-500 font-semibold uppercase">Escalation Reason <span class="text-red-500">*</span></label>
                                <textarea name="escalation_note" rows="2"
                                    class="mt-1 block w-full border-slate-300 rounded text-sm"
                                    placeholder="Why is this being escalated?"
                                    required></textarea>
                            </div>
                            <button type="submit"
                                class="w-full bg-red-600 text-white text-sm font-semibold py-2 px-4 rounded hover:bg-red-700 transition">
                                Escalate Complaint
                            </button>
                        </form>

                    </div>
                </div>
            @endif

            {{-- Audit Trail --}}
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                <h4 class="font-bold text-slate-700 mb-4">Audit Trail</h4>

                @if($complaint->updates->isEmpty())
                    <p class="text-sm text-slate-400">No activity recorded yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($complaint->updates->sortByDesc('updated_at') as $update)
                            <div class="flex gap-4 text-sm border-b border-slate-100 pb-3">
                                <div class="w-32 shrink-0 text-xs text-slate-400">
                                    {{ \Carbon\Carbon::parse($update->updated_at)->format('d M Y H:i') }}
                                </div>
                                <div class="flex-1">
                                    <span class="font-semibold text-slate-700 capitalize">
                                        {{ str_replace('_', ' ', $update->action_type) }}
                                    </span>
                                    @if($update->previous_status && $update->new_status)
                                        <span class="text-xs text-slate-400 ml-2">
                                            ({{ $update->previous_status }} → {{ $update->new_status }})
                                        </span>
                                    @endif
                                    @if($update->update_note)
                                        <p class="text-slate-500 mt-0.5 text-xs">{{ $update->update_note }}</p>
                                    @endif
                                    <p class="text-xs text-slate-300 mt-0.5">
                                        By: {{ $update->officer->full_name ?? 'System' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>