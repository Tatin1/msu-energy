<x-guest-layout>
    <div class="flex items-center justify-center min-h-screen">
        <!-- Solid White Card -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
            
            <!-- Logo / Title -->
            <div class="text-center mb-6">
                <!-- Logo -->
                <img src="{{ asset('images/msuiit-logo.jpg') }}" alt="MSU-IIT Logo" class="mx-auto mb-4 w-20 h-20">

                <h1 class="text-2xl font-bold text-maroon">MSU–IIT Energy Monitoring System</h1>
                <p class="text-sm text-gray-600">Register New Account</p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <!-- Registration Form -->
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="mb-4">
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name"
                        class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-maroon focus:border-maroon bg-[#800000] text-maroon placeholder-white"
                        type="text" name="name" :value="old('name')" placeholder="Enter your name" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
                </div>

                <!-- Email Address -->
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email"
                        class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-maroon focus:border-maroon bg-[#800000] text-maroon placeholder-white"
                        type="email" name="email" :value="old('email')" placeholder="Enter your email" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password"
                        class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-maroon focus:border-maroon bg-[#800000] text-maroon placeholder-white"
                        type="password" name="password" placeholder="Enter your password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                </div>

                <!-- Confirm Password -->
                <div class="mb-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation"
                        class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-maroon focus:border-maroon bg-[#800000] text-maroon placeholder-white"
                        type="password" name="password_confirmation" placeholder="Confirm your password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
                </div>

                <!-- Actions -->
                <div class="flex flex-col gap-2 mt-4">
                    <x-primary-button class="bg-maroon hover:bg-maroon-700 text-white font-semibold px-4 py-2 rounded-lg">
                        {{ __('Register') }}
                    </x-primary-button>

                    <a class="block text-center mt-2 text-maroon hover:text-maroon-700 underline"
                       href="{{ route('login') }}">
                        {{ __('Already registered? Log in') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
