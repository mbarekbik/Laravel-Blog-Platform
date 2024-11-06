<x-admin-layout>
    <x-dashboard-navbar route="{{ route('home') }}"/>

    <div class="dashboard main">
        <img src="{{ asset('images/moon.jpg') }}" id="dashboard__image" alt="dashboard">
        <p class="welcome">Witaj w Panelu Administracyjnym!</p>
        <p class="name_profile">{{ Auth::User()->firstname . ' ' . Auth::User()->lastname }}</p>
        <div class="actions_home">
            <div class="connected">
                @can('post-create')
                    <a href="{{ route('posts.create') }}" class="button">
                        <i class="fa-solid fa-plus"></i>
                        <p>Dodaj post</p>
                    </a>
                @endcan
                @can('post-list')
                    <a href="{{ route('posts.index') }}" class="button">
                        <i class="fa-solid fa-newspaper"></i>
                        <p>Przeglądaj posty</p>
                    </a>
                @endcan
            </div>
            <div class="connected">
                @can('category-create')
                    <a href="{{ route('categories.create') }}" class="button">
                        <i class="fa-solid fa-square-plus"></i>
                        <p>Dodaj kategorię</p>
                    </a>
                @endcan
                @can('category-list')
                    <a href="{{ route('categories.index') }}" class="button">
                        <i class="fa-solid fa-layer-group"></i>
                        <p>Przeglądaj kategorie</p>
                    </a>
                @endcan
            </div>
            <div class="connected">
                @can('user-create')
                    <a href="{{ route('users.create') }}" class="button">
                        <i class="fa-solid fa-user-plus"></i>
                        <p>Dodaj użytkownika</p>
                    </a>
                @endcan
                @can('user-list')
                    <a href="{{ route('users.index') }}" class="button">
                        <i class="fa-solid fa-user-gear"></i>
                        <p>Zarządzaj użytkownikami</p>
                    </a>
                @endcan
            </div>
            @can('comment-list')
                <a href="{{ route('comments.index') }}" class="button">
                    <i class="fa-solid fa-comments"></i>
                    <p>Przeglądaj komentarze</p>
                </a>
            @endcan
            <div class="connected">
                @can('role-create')
                    <a href="{{ route('roles.create') }}" class="button">
                        <i class="fa-solid fa-wrench"></i>
                        <p>Dodaj role</p>
                    </a>
                @endcan
                @can('role-list')
                    <a href="{{ route('roles.index') }}" class="button">
                        <i class="fa-solid fa-toolbox"></i>
                        <p>Przeglądaj role</p>
                    </a>
                @endcan
            </div>
            @can('image-list')
                <a href="{{ route('images.index') }}" class="button">
                    <i class="fa-solid fa-image"></i>
                    <p>Przeglądaj obrazy</p>
                </a>
            @endcan
        </div>
    </div>
</x-admin-layout>
