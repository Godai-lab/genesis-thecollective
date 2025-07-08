<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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

        <title>{{ $title ?? config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">

        @vite(['resources/css/app.css'])

    </head>
    <body class="antialiased">
        @if (isset($header))
            {{ $header }}
        @endif
        <main>
            {{ $slot }}
        </main>
        @if (isset($footer))
            {{ $footer }}
        @endif
    </body>
</html>