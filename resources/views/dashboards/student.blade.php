<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Student Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                {{-- Welcome Message --}}
                <p class="text-gray-900 text-lg font-semibold">
                    Welcome, {{ auth()->user()->full_name }}!
                </p>
                <p class="text-gray-600 mt-1">
                    Campus: {{ auth()->user()->campus->campus_name ?? 'N/A' }}
                </p>
                <p class="text-gray-600">
                    Matric Number: {{ auth()->user()->matric_number ?? 'N/A' }}
                </p>

                {{-- Action Cards --}}
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">

                    {{-- Submit a Complaint --}}
                    <a href="{{ route('complaints.create') }}"
                       class="bg-blue-50 p-4 rounded-lg border border-blue-200 hover:bg-blue-100 transition">
                        <h3 class="font-semibold text-blue-800">Submit a Complaint</h3>
                        <p class="text-sm text-blue-600 mt-1">
                            File a new grievance or academic query
                        </p>
                    </a>

                    {{-- Track My Complaints --}}
                    <a href="{{ route('complaints.track') }}"
                       class="bg-green-50 p-4 rounded-lg border border-green-200 hover:bg-green-100 transition">
                        <h3 class="font-semibold text-green-800">Track My Complaints</h3>
                        <p class="text-sm text-green-600 mt-1">
                            View status of submitted complaints
                        </p>
                    </a>

                    {{-- Ask the Chatbot --}}
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <h3 class="font-semibold text-purple-800">Ask the Chatbot</h3>
                        <p class="text-sm text-purple-600 mt-1">
                            Get instant answers to common questions
                        </p>
                        <p class="text-xs text-purple-400 mt-2">Coming in Phase 5</p>
                    </div>

                </div>

                {{-- Recent Complaints Section --}}
                <div class="mt-8">
                    <h3 class="text-gray-800 font-semibold text-base mb-3">
                        My Recent Complaints
                    </h3>

                    @php
                        $complaints = \App\Models\Complaint::where('submitter_id', auth()->id())
                                        ->latest()
                                        ->take(5)
                                        ->get();
                    @endphp

                    @if($complaints->isEmpty())
                        <div class="bg-gray-50 border border-gray-200 rounded p-4 text-center">
                            <p class="text-gray-500 text-sm">
                                You have not submitted any complaints yet.
                            </p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                    <tr>
                                        <th class="px-4 py-2">Title</th>
                                        <th class="px-4 py-2">Category</th>
                                        <th class="px-4 py-2">Urgency</th>
                                        <th class="px-4 py-2">Status</th>
                                        <th class="px-4 py-2">Submitted</th>
                                        <th class="px-4 py-2">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($complaints as $complaint)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 font-medium">
                                            {{ Str::limit($complaint->complaint_title, 30) }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $complaint->category->category_name ?? 'Processing' }}
                                        </td>
                                        <td class="px-4 py-2 capitalize">
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                                {{ $complaint->urgency_level === 'critical' ? 'bg-red-100 text-red-700' : '' }}
                                                {{ $complaint->urgency_level === 'high' ? 'bg-orange-100 text-orange-700' : '' }}
                                                {{ $complaint->urgency_level === 'medium' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                                {{ $complaint->urgency_level === 'low' ? 'bg-green-100 text-green-700' : '' }}">
                                                {{ ucfirst($complaint->urgency_level) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 capitalize">
                                            {{ ucfirst(str_replace('_', ' ', $complaint->current_status)) }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-500">
                                            {{ $complaint->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('complaints.track') }}?token={{ $complaint->tracking_token }}"
                                               class="text-indigo-600 hover:underline text-xs">
                                                Track
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
    </div>
</x-app-layout>