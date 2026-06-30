<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Full Name -->
        <div>
            <x-input-label for="full_name" :value="__('Full Name')" />
            <x-text-input id="full_name" class="block mt-1 w-full" type="text"
                name="full_name" :value="old('full_name')" required autofocus />
            <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Institutional Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email"
                name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Matric Number -->
        <div class="mt-4">
            <x-input-label for="matric_number" :value="__('Matric Number (Students only)')" />
            <x-text-input id="matric_number" class="block mt-1 w-full" type="text"
                name="matric_number" :value="old('matric_number')" placeholder="e.g. DE.2022/7545" />
            <x-input-error :messages="$errors->get('matric_number')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone_number" :value="__('Phone Number')" />
            <x-text-input id="phone_number" class="block mt-1 w-full" type="text"
                name="phone_number" :value="old('phone_number')" placeholder="e.g. 08012345678" />
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
        </div>

        <!-- Campus -->
        <div class="mt-4">
            <x-input-label for="campus_id" :value="__('Campus')" />
            <select id="campus_id" name="campus_id"
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">-- Select Campus --</option>
                @foreach($campuses as $campus)
                    <option value="{{ $campus->id }}"
                        {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                        {{ $campus->campus_name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('campus_id')" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="mt-4">
            <x-input-label for="role_id" :value="__('Role')" />
            <select id="role_id" name="role_id"
                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                <option value="">-- Select Role --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}"
                        {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->role_name }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password"
                name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                type="password" name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>