<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Complaint Submitted Successfully
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-8 text-center">

                <div class="text-green-500 text-6xl mb-4">✓</div>

                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                    Your complaint has been received
                </h3>

                <p class="text-gray-600 mb-6">
                    Save your tracking token below. You can use it to check
                    the status of your complaint at any time without logging in.
                </p>

                {{-- Tracking Token Display --}}
                <div class="bg-gray-100 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-500 mb-1">Your Tracking Token</p>
                    <p class="font-mono text-lg font-bold text-indigo-700 break-all">
                        {{ $complaint->tracking_token }}
                    </p>
                </div>

                {{-- Complaint Details --}}
                <div class="text-left bg-blue-50 rounded p-4 mb-6">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <span class="text-gray-500">Title:</span>
                        <span class="font-medium">{{ $complaint->complaint_title }}</span>

                        <span class="text-gray-500">Category:</span>
                        <span class="font-medium capitalize">
                            {{ $complaint->category->category_name ?? 'Processing...' }}
                        </span>

                        <span class="text-gray-500">Urgency:</span>
                        <span class="font-medium capitalize
                            {{ $complaint->urgency_level === 'critical' ? 'text-red-600' : '' }}
                            {{ $complaint->urgency_level === 'high' ? 'text-orange-600' : '' }}
                            {{ $complaint->urgency_level === 'medium' ? 'text-yellow-600' : '' }}
                            {{ $complaint->urgency_level === 'low' ? 'text-green-600' : '' }}">
                            {{ ucfirst($complaint->urgency_level) }}
                        </span>

                        <span class="text-gray-500">Status:</span>
                        <span class="font-medium capitalize">
                            {{ ucfirst($complaint->current_status) }}
                        </span>

                        <span class="text-gray-500">Anonymous:</span>
                        <span class="font-medium">
                            {{ $complaint->is_anonymous ? 'Yes — identity encrypted' : 'No' }}
                        </span>
                    </div>
                </div>

                <div class="flex gap-3 justify-center">
                    <a href="{{ route('complaints.track') }}?token={{ $complaint->tracking_token }}"
                       class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                        Track This Complaint
                    </a>
                    <a href="{{ route('student.dashboard') }}"
                       class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>