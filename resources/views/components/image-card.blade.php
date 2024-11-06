<div class="image" onclick="getFileInfo('{{$image['directory']}}', '{{$image['fullname']}}')">
    <img loading="lazy" src="{{ asset($image['path']) }}" alt="{{ $image['name'] }}">
    <div class="info">
        <p class="directory"><i class="fa-solid fa-folder"></i> {{ $image['directory'] }}</p>
        <p class="filename">{{ $image['name'] }}</p>
        @if (!empty($image['uniqid']))
            <p class="uniqid"><i class="fa-solid fa-hashtag"></i> {{ $image['uniqid'] }}</p>
        @endif
        <p class="size"><i class="fa-solid fa-database"></i> {!! formatBytes($image['size']) !!}</p>
        <p class="usage_count"><i class="fa-solid fa-recycle"></i> Użyto: <span>{{ $image['usage_count'] }}</span></p>
    </div>
</div>
