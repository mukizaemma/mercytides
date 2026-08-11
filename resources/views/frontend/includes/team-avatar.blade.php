@props([
    'src' => null,
    'alt' => '',
    'focus' => '50% 18%',
    'size' => 'md',
])

<div class="team-avatar{{ $size === 'sm' ? ' team-avatar--sm' : '' }}" style="--team-focus: {{ $focus }};">
    @if($src)
        <img src="{{ $src }}" alt="{{ $alt }}">
    @else
        <div class="team-avatar__placeholder" aria-hidden="true">
            <i class="fas fa-user fa-2x"></i>
        </div>
    @endif
</div>
