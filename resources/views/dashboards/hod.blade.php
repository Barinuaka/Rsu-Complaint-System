<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            HOD Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-gray-900 text-lg font-semibold">
                    Welcome, {{ auth()->user()->full_name }}
                </p>
                <p class="text-gray-600 mt-1">Role: Head of Department</p>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <h3 class="font-semibold text-red-800">Pending Complaints</h3>
                        <p class="text-3xl font-bold text-red-600 mt-2">0</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                        <h3 class="font-semibold text-yellow-800">In Review</h3>
                        <p class="text-3xl font-bold text-yellow-600 mt-2">0</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <h3 class="font-semibold text-green-800">Resolved This Week</h3>
                        <p class="text-3xl font-bold text-green-600 mt-2">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>