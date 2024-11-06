<!DOCTYPE html>
<html lang="pl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Blog</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
    <script src="https://kit.fontawesome.com/15901ecbea.js" crossorigin="anonymous"></script>
    <script>
        const currentTheme = localStorage.getItem("theme") ?? "light";
        if (currentTheme === "dark") {
            document.documentElement.setAttribute('data-theme', 'dark');
        } else if (currentTheme === "light") {
            document.documentElement.setAttribute('data-theme', 'light');
        }
    </script>
    @yield('scripts')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <x-header-navbar />

    @if(!Auth::User())
        <x-change-theme />
    @endif

    {{ $slot }}

    @if(Auth::User())
        <x-user-panel />
    @endif

    <footer>
        <p>Mateusz Zaborski ● 2023</p>
    </footer>

</body>
</html>
