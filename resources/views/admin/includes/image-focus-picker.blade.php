@props([
    'imageUrl' => null,
    'focus' => '50 18',
    'name' => 'image_focus',
])

@php
    $parsed = \App\Models\Team::parseImageFocus($focus);
    $value = \App\Models\Team::normalizeImageFocus($focus);
@endphp

<div class="image-focus-picker mt-3" data-image-focus>
    <label class="form-label mb-1">Photo crop</label>
    <p class="text-muted small mb-2">Click the face (or drag the sliders) so it sits in the centre of the circle.</p>

    <div class="image-focus-picker__row">
        <button type="button" class="image-focus-picker__preview" data-focus-preview aria-label="Click to set the face position">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="Crop preview" data-focus-image style="object-position: {{ $parsed['x'] }}% {{ $parsed['y'] }}%;">
            @else
                <span class="image-focus-picker__empty">Upload a photo to preview the crop</span>
            @endif
            <span class="image-focus-picker__pin" data-focus-pin style="left: {{ $parsed['x'] }}%; top: {{ $parsed['y'] }}%;"></span>
        </button>

        <div class="image-focus-picker__sliders">
            <label class="form-label small mb-1" for="{{ $name }}_x">Horizontal</label>
            <input type="range" class="form-range" id="{{ $name }}_x" min="0" max="100" step="1" value="{{ (int) round($parsed['x']) }}" data-focus-x>

            <label class="form-label small mb-1 mt-2" for="{{ $name }}_y">Vertical</label>
            <input type="range" class="form-range" id="{{ $name }}_y" min="0" max="100" step="1" value="{{ (int) round($parsed['y']) }}" data-focus-y>
        </div>
    </div>

    <input type="hidden" name="{{ $name }}" value="{{ $value }}" data-focus-input>
</div>
