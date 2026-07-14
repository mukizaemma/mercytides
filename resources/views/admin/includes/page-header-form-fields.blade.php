@php
    $header = $header ?? null;
    $isEdit = $header instanceof \App\Models\PageHeader;
    $isBuiltIn = $isEdit && array_key_exists($header->page_key, \App\Models\PageHeader::catalog());
    $formAction = $isEdit
        ? route('pageHeaders.update', $header->id, false)
        : route('pageHeaders.store', [], false);
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" data-turbo="false" autocomplete="off">
    @csrf
    <div class="mb-3">
        <label class="form-label">Label <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="label" required value="{{ old('label', $isEdit ? $header->label : '') }}" placeholder="e.g. Sponsor a young mother">
    </div>

    @unless($isBuiltIn)
        <div class="mb-3">
            <label class="form-label">Page key @if(!$isEdit)<span class="text-muted">(optional)</span>@endif</label>
            <input type="text" class="form-control" name="page_key" value="{{ old('page_key', $isEdit ? $header->page_key : '') }}" placeholder="custom_page_key" pattern="[a-z0-9_\-]+">
            <small class="text-muted">Lowercase letters, numbers, dashes, underscores. Auto-generated if left blank.</small>
        </div>
    @else
        <div class="mb-3">
            <label class="form-label">Page key</label>
            <input type="text" class="form-control" value="{{ $header->page_key }}" disabled>
        </div>
    @endunless

    <div class="mb-3">
        <label class="form-label">Header image @if(!$isEdit)<span class="text-danger">*</span>@endif</label>
        <input type="file" class="form-control" name="image" {{ $isEdit ? '' : 'required' }} accept="image/*" data-image-preset="hero">
        <small class="text-muted">Wide landscape photos work best for the breadcrumb hero.</small>
        @if($isEdit && $header->imageUrl())
            <img src="{{ $header->imageUrl() }}" alt="" class="mt-2 rounded border" width="220" height="90" style="object-fit:cover;">
        @endif
    </div>

    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="page_header_default_{{ $isEdit ? $header->id : 'new' }}" {{ old('is_default', $isEdit && $header->is_default) ? 'checked' : '' }}>
        <label class="form-check-label" for="page_header_default_{{ $isEdit ? $header->id : 'new' }}">
            Use as site default when a page has no specific image
        </label>
    </div>

    <div class="d-flex flex-wrap gap-2 justify-content-end pt-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update header' : 'Save header' }}</button>
    </div>
</form>
