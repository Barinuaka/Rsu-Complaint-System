<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Complaint — RSU Complaint System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">

    <div class="bg-white rounded-lg shadow-sm p-8 w-full max-w-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-2 text-center">
            Track Your Complaint
        </h1>
        <p class="text-gray-500 text-sm text-center mb-6">
            No login required. Enter your UUID tracking token below.
        </p>

        <form method="GET" action="{{ route('complaints.track') }}">
            <div class="flex gap-2">
                <input type="text" name="token"
                    value="{{ request('token') }}"
                    placeholder="Paste your tracking token here"
                    class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm font-mono"
                    required />
                <button type="submit"
                    class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                    Track
                </button>
            </div>
        </form>

        @if($complaint)
        <div class="mt-6 border-t pt-6">
            <h2 class="font-semibold text-gray-800 mb-3">
                {{ $complaint->complaint_title }}
            </h2>

            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Status</span>
                    <span class="font-semibold capitalize bg-blue-100 text-blue-800
                                 px-2 py-0.5 rounded">
                        {{ ucfirst(str_replace('_', ' ', $complaint->current_status)) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Category</span>
                    <span class="font-medium">
                        {{ $complaint->category->category_name ?? 'Processing' }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Urgency</span>
                    <span class="font-medium capitalize">
                        {{ ucfirst($complaint->urgency_level) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Submitted</span>
                    <span class="font-medium">
                        {{ $complaint->created_at->format('d M Y, H:i') }}
                    </span>
                </div>
                @if($complaint->resolution_note)
                <div class="mt-4 bg-green-50 border border-green-200 rounded p-3">
                    <p class="text-sm font-semibold text-green-800">Resolution Note</p>
                    <p class="text-sm text-green-700 mt-1">
                        {{ $complaint->resolution_note }}
                    </p>
                </div>
                @endif
            </div>
        </div>
        @elseif(request('token'))
        <div class="mt-6 bg-red-50 border border-red-200 rounded p-4 text-center">
            <p class="text-red-700 text-sm">
                No complaint found with that tracking token.
                Please check and try again.
            </p>
        </div>
        @endif

        <p class="text-center text-xs text-gray-400 mt-6">
            RSU Smart Complaint System — NDPA 2023 Compliant
        </p>
    </div>

</body>
</html>