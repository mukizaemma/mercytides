@php
    $id = $id ?? 'adminModal';
    $title = $title ?? 'Dialog';
    $sizeClass = $sizeClass ?? 'modal-lg';
    $scrollable = $scrollable ?? true;
@endphp
<div class="modal fade admin-modal" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog {{ $sizeClass }} {{ $scrollable ? 'modal-dialog-scrollable' : '' }}">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {!! $body ?? '' !!}
            </div>
        </div>
    </div>
</div>
