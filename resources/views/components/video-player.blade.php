{{-- 
    Plyr Video Player Component
    
    Usage:
    <x-video-player :video="$mediaItem" autoplay loop muted :controls="false" />
--}}
@props([
    'video',
    'autoplay' => false,
    'loop' => false,
    'muted' => false,
    'controls' => true,
    'class' => '',
])

<div class="video-player-wrapper {{ $class }}">
    <video 
        class="plyr-video"
        @if($autoplay) autoplay @endif
        @if($loop) loop @endif
        @if($muted) muted @endif
        @if($controls) controls @endif
        playsinline
        poster="{{ $video->hasGeneratedConversion('video_thumb') ? $video->getUrl('video_thumb') : $video->getUrl() }}"
    >
        <source src="{{ $video->getUrl() }}" type="{{ $video->mime_type }}">
        Tu navegador no soporta video HTML5.
    </video>
</div>

@push('styles')
<style>
.video-player-wrapper {
    position: relative;
    width: 100%;
    padding-bottom: 56.25%; /* 16:9 aspect ratio */
    overflow: hidden;
    background: #000;
}

.video-player-wrapper .plyr-video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Plyr custom theme */
.plyr--video {
    --plyr-color-main: #1A5229;
}
</style>
@endpush
