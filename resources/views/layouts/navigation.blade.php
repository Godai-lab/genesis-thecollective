<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-18">
            <div class="flex">
                                               <!-- Logo -->
                <div class="shrink-0 flex flex-col items-center mt-2">
                    {{-- <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-6 w-auto fill-current text-white" />
                    </a> --}}
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/kravas-logo-horizontal.png') }}" class="block h-14 w-auto" alt="Kravas Logo" />
                    </a>
                            
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
                @can('haveaccess','subscription.index')
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('subscription.index')" :active="request()->routeIs('subscription.index','subscription.create','subscription.edit')">
                        {{ __('Subscripción') }}
                    </x-nav-link>
                </div>
                @endcan
                @can('haveaccess','subscription.index')
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('planServiceLimits.index')" :active="request()->routeIs('planServiceLimits.index','planServiceLimits.create','planServiceLimits.edit')">
                        {{ __('ServiciosPlanes') }}
                    </x-nav-link>
                </div>
                @endcan
                @can('haveaccess','subscription.index')
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('plans.index')" :active="request()->routeIs('plans.index','plans.create','plans.edit')">
                        {{ __('Planes') }}
                    </x-nav-link>
                </div>
                @endcan
                @can('haveaccess','generated.index')
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('generated.index')" :active="request()->routeIs('generated.index','generated.create','generated.edit')">
                        {{ __('Generados') }}
                    </x-nav-link>
                </div>
                @endcan
                @can('haveaccess','user.index')
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('user.index')" :active="request()->routeIs('user.index','user.create','user.edit')">
                        {{ __('Usuarios') }}
                    </x-nav-link>
                </div>
                @endcan
                
                @can('haveaccess','account.index')
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('account.index')" :active="request()->routeIs('account.index','account.create','account.edit')">
                        {{ __('Cuentas') }}
                    </x-nav-link>
                </div>
                @endcan
                @can('haveaccess','role.index')
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('role.index')" :active="request()->routeIs('role.index','role.create','role.edit')">
                        {{ __('Roles') }}
                    </x-nav-link>
                </div>
                @endcan
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>
        @can('haveaccess','generated.index')
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('generated.index')" :active="request()->routeIs('generated.index')">
                {{ __('Generados') }}
            </x-responsive-nav-link>
        </div>
        @endcan
        @can('haveaccess','user.index')
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('user.index')" :active="request()->routeIs('user.index')">
                {{ __('Usuarios') }}
            </x-responsive-nav-link>
        </div>
        @endcan
        @can('haveaccess','account.index')
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('account.index')" :active="request()->routeIs('account.index')">
                {{ __('Cuentas') }}
            </x-responsive-nav-link>
        </div>
        @endcan
        @can('haveaccess','role.index')
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('role.index')" :active="request()->routeIs('role.index')">
                {{ __('Roles') }}
            </x-responsive-nav-link>
        </div>
        @endcan
        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Perfil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Cerrar sesión') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
