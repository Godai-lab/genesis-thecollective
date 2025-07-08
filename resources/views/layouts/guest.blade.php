<!DOCTYPE html>
<html class="" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-6FMRMN31JD"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-6FMRMN31JD');
        </script>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <link rel="stylesheet" href="{{ asset('css/style.css') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col gap-28 z-10 sm:justify-between relative items-end pt-6 sm:pt-0">
            <!--<div class="flex flex-col w-full justify-center content-center items-center">
                <a class="" href="{{route('home')}}"><img class="h-16 sm:mt-5" src="{{ asset('images/kravas-logo-horizontal.png') }}" alt=""></a>
                {{-- <a class="" href="{{route('home')}}"><img class="w-12 sm:mt-10" src="{{ asset('images/god-ai-isologo.png') }}" alt=""></a> --}}
            </div>-->
            <div class="flex flex-row sm:w-[52%] w-full px-4">
                <div class="flex flex-col w-full sm:max-w-md gap-16">
                   <!-- <div class="flex">
                        <a class="" href="{{route('home')}}"><img class="h-16 sm:mt-5" src="{{ asset('images/kravas-logo-horizontal.png') }}" alt=""></a>
                        <a href="/">
                            <div class="text-7xl text-center">Génesis</div>
                            {{-- <x-application-logo class="w-52 h-16 fill-current text-white" /> --}}
                        </a>
                    </div>-->
                    <div class="shrink-0 flex flex-col items-start w-full mt-8">
                    
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('images/kravas-logo-horizontal.png') }}" class="block h-14 w-auto" alt="Kravas Logo" />
                    </a>
                    <div class="text-7xl">Génesis</div>
                            
                </div>
        
                    <div class="overflow-hidden">
                        {{ $slot }}
                    </div>
                </div>
            </div>
            <div class="min-h-[80px]"></div>
        </div>
        <div class="image-wrapper h-full absolute top-0 left-0 w-full z-0">
            <img class="h-full w-full object-cover object-top absolute" src="{{ asset('images/godai-bg-guest.jpg') }}" alt="">
        </div>
    </body>
</html>

