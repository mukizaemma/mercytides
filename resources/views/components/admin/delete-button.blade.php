@props([
    'action',
    'confirm' => 'Are you sure you want to delete this item?',
    'label' => 'Delete',
])

{{-- Destructive actions must be POST so Turbo cannot prefetch/delete on hover. --}}
<form
    action="{{ $action }}"
    method="POST"
    class="d-inline"
    data-turbo="false"
    data-no-sweet-submit="true"
    onsubmit="return confirm(@js($confirm))"
>
    @csrf
    <button type="submit" {{ $attributes->merge(['class' => 'btn btn-outline-danger btn-sm']) }}>
        {{ $label }}
    </button>
</form>
