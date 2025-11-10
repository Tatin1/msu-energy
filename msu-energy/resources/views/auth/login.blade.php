<x-guest-layout>
  <div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
      
      <!-- Logo / Title -->
      <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-maroon">MSU–IIT Energy Monitoring System</h1>
        <p class="text-sm text-gray-600">Administrator Login Portal</p>
      </div>

      <!-- Session Status -->
      <x-auth-session-status class="mb-4" :status="session('status')" />

      <!-- Login Form -->
      <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-4">
          <x-input-label for="email" :value="__('Email')" />
          <x-text-input id="email" 
              class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-maroon focus:border-maroon"
              type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
          <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-4">
          <x-input-label for="password" :value="__('Password')" />
          <x-text-input id="password"
              class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:ring-maroon focus:border-maroon"
              type="password" name="password" required autocomplete="current-password" />
          <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mb-4">
          <input id="remember_me" type="checkbox" 
                 class="rounded border-gray-300 text-maroon shadow-sm focus:ring-maroon" name="remember">
          <label for="remember_me" class="ml-2 text-sm text-gray-700">{{ __('Remember me') }}</label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
          @if (Route::has('password.request'))
            <a class="text-sm text-maroon hover:text-maroon-700 underline" 
               href="{{ route('password.request') }}">
              {{ __('Forgot your password?') }}
            </a>
          @endif

          <x-primary-button class="bg-maroon hover:bg-maroon-700 text-white font-semibold px-4 py-2 rounded-lg">
            {{ __('Log in') }}
          </x-primary-button>
        </div>
      </form>

      <!-- Default Admin Info -->
      <div class="mt-6 text-center text-xs text-gray-500">
        <p><strong>Default Admin Account:</strong></p>
        <p>Email: <code>admin@msuiit.edu.ph</code></p>
        <p>Password: <code>admin123</code></p>
      </div>
    </div>
  </div>
</x-guest-layout>
