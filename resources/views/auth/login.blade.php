<x-guest-layout>
    <x-slot name="title">Génesis - Login</x-slot>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="username" :value="__('Usuario')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-black dark:border-gray-700 text-black shadow-sm focus:ring-1 focus:ring-black dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ml-2 text-sm text-black dark:text-gray-400">{{ __('Recuérdame') }}</span>
            </label>
        </div>

        <div class="block mt-4">
            <p class="text-sm">
                Al ingresar aceptas todos nuestros 
                <a class="underline text-sm text-black dark:text-gray-400 hover:text-black dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-black dark:focus:ring-offset-gray-800" target="_blank" href="{{ route('termsConditions') }}">
                {{ __('términos y condiciones') }}
                </a>
            </p>
        </div>
        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-black dark:text-gray-400 hover:text-black dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-black dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif

            {{-- <a class="underline ml-3 text-sm text-black dark:text-gray-400 hover:text-black dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-1 focus:ring-offset-2 focus:ring-black dark:focus:ring-offset-gray-800" href="{{ route('register') }}">
                {{ __('¿Quieres registrarte?') }}
            </a> --}}

            <x-primary-button class="ml-3">
                {{ __('Iniciar') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
