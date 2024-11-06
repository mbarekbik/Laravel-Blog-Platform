<!DOCTYPE html>
<html lang="pl" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}" />
    <script src="https://kit.fontawesome.com/15901ecbea.js" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link id="theme-stylesheet" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-default/default.css">
    <script>
        const currentTheme = localStorage.getItem("theme") ?? "light";
        if (currentTheme === "dark") {
            document.documentElement.setAttribute('data-theme', 'dark');
            document.getElementById('theme-stylesheet').setAttribute('href', 'https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@5/dark.css');
        } else if (currentTheme === "light") {
            document.documentElement.setAttribute('data-theme', 'light');
        }
    </script>
    @yield('scripts')
    @vite(['resources/css/admin.css', 'resources/js/app.js'])
</head>
@if( Request::Url() == route('posts.create') OR $edit)
    <body style="display: block;">
@else
    <body>
@endif

    @if(!Auth::User())
        <x-change-theme />
    @endif

    {{ $slot }}

    <x-user-panel />

</body>
</html>
