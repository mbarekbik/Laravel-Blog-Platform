<header>
    <a href="{{ $route }}"><i class="fa-solid fa-left-long"></i> Powrót</a>
    <div class="profile">
        <img src="{{ asset(Auth::user()->image_path) }}" alt="profile" class="profile_img">
        <i class="fa-solid fa-angles-down"></i>
        <span class="notifications_count">{{ $unreadNotifications }}</span>
    </div>
</header>
