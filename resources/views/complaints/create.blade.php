<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Submit a Complaint or Query
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                {{-- Info box --}}
                <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <strong>Your complaint will be automatically classified</strong>
                        by our AI system and routed to the appropriate officer.
                        You will receive a tracking token upon submission.
                    </p>
                </div>

                <form method="POST" action="{{ route('complaints.store') }}"
                      enctype="multipart/form-data">
                    @csrf

                    {{-- Complaint Title --}}
                    <div class="mb-4">
                        <x-input-label for="complaint_title" value="Complaint Title" />
                        <x-text-input id="complaint_title" name="complaint_title"
                            type="text" class="block mt-1 w-full"
                            placeholder="Brief summary of your complaint"
                            :value="old('complaint_title')" required />
                        <x-input-error :messages="$errors->get('complaint_title')" class="mt-2" />
                    </div>

                    {{-- Complaint Text --}}
                    <div class="mb-4">
                        <x-input-label for="complaint_text" value="Describe Your Complaint" />
                        <textarea id="complaint_text" name="complaint_text"
                            rows="6"
                            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                            placeholder="Please describe your complaint in detail. The more detail you provide, the better our system can classify and route it appropriately."
                            required>{{ old('complaint_text') }}</textarea>
                        <x-input-error :messages="$errors->get('complaint_text')" class="mt-2" />
                    </div>

                    {{-- Evidence Upload --}}
                    <div class="mb-4">
                        <x-input-label for="evidence_file"
                            value="Attach Evidence (Optional)" />
                        <input id="evidence_file" name="evidence_file" type="file"
                            class="block mt-1 w-full text-sm text-gray-500" />
                        <p class="text-xs text-gray-400 mt-1">
                            Accepted: PDF, JPG, PNG, DOC, DOCX. Max 5MB.
                        </p>
                        <x-input-error :messages="$errors->get('evidence_file')" class="mt-2" />
                    </div>

                    {{-- Anonymous Toggle --}}
                    <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded p-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="is_anonymous" value="1"
                                class="mt-1 rounded border-gray-300"
                                {{ old('is_anonymous') ? 'checked' : '' }}>
                            <div>
                                <span class="font-semibold text-yellow-900">
                                    Submit Anonymously
                                </span>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Your identity will be encrypted and stored separately.
                                    You will only be identifiable by legal authority under
                                    NDPA 2023. You will receive a UUID token to track
                                    your complaint without revealing your identity.
                                </p>
                            </div>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>
                            Submit Complaint
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>