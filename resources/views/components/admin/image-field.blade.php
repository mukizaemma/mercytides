@props([
    'label' => 'Image',
    'name' => 'image',
    'libraryName' => null,
    'multiple' => false,
    'current' => null,
    'currentUrl' => null,
    'legacyDir' => null,
    'help' => null,
    'required' => false,
    'preset' => null,
])

@php
    $baseName = rtrim((string) $name, '[]');
    $libraryName = $libraryName ?: ($multiple ? $baseName.'_paths' : $baseName.'_path');
    $uid = 'media-picker-'.str_replace(['[', ']', '.'], '-', $name).'-'.substr(md5($label.$name.$libraryName), 0, 8);
    $currentPaths = collect(is_array($current) ? $current : ($current ? [$current] : []))
        ->filter()
        ->map(fn ($p) => ltrim(str_replace('\\', '/', (string) $p), '/'))
        ->values();
    if (! $currentUrl && $currentPaths->isNotEmpty()) {
        $currentUrl = \App\Support\StorageImage::url($currentPaths->first(), $legacyDir);
    }
    $maxKb = (int) round(((int) config('image.max_bytes', 700 * 1024)) / 1024);
@endphp

<div
    class="admin-media-picker"
    data-media-picker
    data-mode="{{ $multiple ? 'multiple' : 'single' }}"
    data-library-url="{{ route('mediaLibrary.index') }}"
    data-library-name="{{ $multiple ? $libraryName.'[]' : $libraryName }}"
    id="{{ $uid }}"
>
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
        <label class="form-label mb-0">{{ $label }}@if($required) <span class="text-danger">*</span>@endif</label>
        <div class="btn-group btn-group-sm admin-media-picker__tabs" role="group" aria-label="Image source">
            <button type="button" class="btn btn-outline-primary active" data-media-tab="upload">Upload new</button>
            <button type="button" class="btn btn-outline-primary" data-media-tab="library">Choose existing</button>
        </div>
    </div>

    <p class="text-muted small mb-2">
        {{ $help ?: 'Upload a new file or reuse an image already in the library (avoids duplicates).' }}
        Max upload size {{ $maxKb }} KB — larger files are resized; smaller files stay as-is.
    </p>

    <div class="admin-media-picker__panel" data-media-panel="upload">
        <input
            type="file"
            class="form-control"
            name="{{ $name }}"
            accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
            @if($multiple) multiple @endif
            @if($preset) data-image-preset="{{ $preset }}" @endif
            data-media-upload
        >
    </div>

    <div class="admin-media-picker__panel d-none" data-media-panel="library">
        <div class="input-group input-group-sm mb-2">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="search" class="form-control" placeholder="Search existing images…" data-media-search autocomplete="off">
        </div>
        <div class="admin-media-picker__grid" data-media-grid>
            <div class="admin-media-picker__empty text-muted small py-4 text-center">Loading images…</div>
        </div>
        <div class="admin-media-picker__selected mt-2" data-media-selected>
            @if($multiple)
                <div class="d-flex flex-wrap gap-2" data-media-selected-list></div>
            @endif
        </div>
    </div>

    @if($multiple)
        <div data-media-library-inputs></div>
    @else
        <input type="hidden" name="{{ $libraryName }}" value="" data-media-library-input>
    @endif

    @unless($multiple)
        @if($currentUrl)
            <div class="admin-media-picker__current-inline mt-2" data-media-current-inline>
                <img src="{{ $currentUrl }}" alt="Current image" class="admin-media-picker__thumb">
                <span class="small text-muted ms-2">Current image — upload or choose another to replace.</span>
            </div>
        @endif
    @endunless
</div>
