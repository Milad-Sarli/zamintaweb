<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ in_array(app()->getLocale(), ['fa', 'ar', 'he']) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'کالای پزشکی پارس طب ۱۱۵') }}</title>

    {{-- <link rel="preconnect" href="https://fonts.bunny.net"> --}}
    {{-- <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" /> --}}
    <link href="/fonts/fonts.css" rel="stylesheet" />
    {{-- <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" /> --}}

    @routes
    @vite(['resources/js/app.ts'])
    @inertiaHead

    {{-- Inline script to detect system dark mode preference and apply it immediately --}}
    <script>
        (function() {
            try {
                const savedAppearance = localStorage.getItem('appearance') || 'system';
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (savedAppearance === 'dark' || (savedAppearance === 'system' && prefersDark)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (e) {
                /* console.error('Error applying theme:'); */
            }
        })();
    </script>

    {{-- Inline style to set the HTML background color based on our theme in app.css --}}
    <style>
        html {
            background-color: hsl(0 0% 100%);
        }

        html.dark {
            background-color: hsl(0 0% 3.9%);
        }
    </style>
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
