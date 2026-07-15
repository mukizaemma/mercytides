@extends('layouts.adminbase')

@section('title', 'Sponsorship profiles')

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav" data-turbo-permanent>
        @include('admin.includes.sidenav')
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h1>Sponsorship profiles</h1>
                        <p class="text-muted mb-0">
                            Manage children, young mothers, and families available for sponsorship.
                            <strong>{{ $totalCount ?? $profiles->count() }}</strong> profile{{ ($totalCount ?? $profiles->count()) === 1 ? '' : 's' }} in total
                            @if(!empty($typeFilter))
                                · showing <strong>{{ $filteredCount ?? $profiles->count() }}</strong> for this type
                            @endif
                            .
                        </p>
                    </div>
                    <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#sponsorshipCreateModal">
                        <i class="fa fa-plus me-1"></i> Add profile
                    </button>
                </div>

                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session()->get('success') }}</div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session()->get('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <div class="fw-semibold mb-1">Please fix the form errors and try again.</div>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <span class="text-muted small me-1">Filter:</span>
                            <a href="{{ route('sponsorship.index') }}" class="btn btn-sm {{ empty($typeFilter) ? 'btn-primary' : 'btn-outline-secondary' }}">All types ({{ $totalCount ?? $profiles->count() }})</a>
                            @foreach($types as $key => $meta)
                                @php $typeCount = (int) (($typeCounts[$key] ?? 0)); @endphp
                                <a href="{{ route('sponsorship.index', ['type' => $key]) }}" class="btn btn-sm {{ ($typeFilter ?? '') === $key ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $meta['label'] }} ({{ $typeCount }})</a>
                            @endforeach
                            <button type="button" class="btn btn-sm btn-outline-primary ms-auto" data-bs-toggle="modal" data-bs-target="#sponsorshipSupportOptionsModal">
                                <i class="fa fa-sliders-h me-1"></i> Edit ways to support
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive admin-table-wrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Monthly need</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($profiles as $profile)
                                        <tr>
                                            <td>
                                                <img src="{{ \App\Models\Sponsorship::publicImageUrl($profile->image) }}" alt="{{ $profile->displayName() }}" class="rounded border" width="72" height="96" style="object-fit:cover;">
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $profile->displayName() }}</div>
                                                <small class="text-muted">{{ $profile->age ? 'Age '.$profile->age : '—' }}</small>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $profile->typeLabel() }}</span></td>
                                            <td>
                                                <span class="badge {{ $profile->isAvailable() ? 'bg-warning text-dark' : 'bg-success' }}">{{ $profile->status }}</span>
                                                @if(!$profile->isPublished())
                                                    <span class="badge bg-secondary">Draft</span>
                                                @endif
                                            </td>
                                            <td>{{ $profile->monthly_need ? '$'.$profile->monthly_need : '—' }}</td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ $profile->profileRoute() }}" class="btn btn-outline-secondary" target="_blank" rel="noopener">View</a>
                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sponsorshipEditModal{{ $profile->id }}">Edit</button>
                                                    <a href="{{ route('destroySponsorship', $profile->id) }}" class="btn btn-outline-danger" onclick="return confirm('Delete this sponsorship profile?')">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="border-0">
                                                <div class="admin-empty-state">
                                                    <p class="mb-0">No sponsorship profiles yet. Add a child, young mother, or family to publish on the website.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>

{{-- Create modal --}}
<div class="modal fade admin-modal" id="sponsorshipCreateModal" tabindex="-1" aria-labelledby="sponsorshipCreateModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sponsorshipCreateModalLabel">Add sponsorship profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @include('admin.includes.sponsorship-form-fields', [
                    'formProfile' => null,
                    'forceCreate' => true,
                    'types' => $types,
                ])
            </div>
        </div>
    </div>
</div>

{{-- Edit modals: include forms directly to avoid id/variable collision from pre-rendered bodies --}}
@foreach($profiles as $editProfile)
    <div class="modal fade admin-modal" id="sponsorshipEditModal{{ $editProfile->id }}" tabindex="-1" aria-labelledby="sponsorshipEditModalLabel{{ $editProfile->id }}" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sponsorshipEditModalLabel{{ $editProfile->id }}">Edit {{ $editProfile->displayName() }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('admin.includes.sponsorship-form-fields', [
                        'formProfile' => $editProfile,
                        'types' => $types,
                    ])
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- Ways to support editor --}}
@php
    $editableSupportOptions = old('support_options', $supportOptions ?? \App\Support\SponsorshipSupportOptions::all());
    if (! is_array($editableSupportOptions) || $editableSupportOptions === []) {
        $editableSupportOptions = \App\Support\SponsorshipSupportOptions::defaults();
    }
@endphp
<div class="modal fade admin-modal" id="sponsorshipSupportOptionsModal" tabindex="-1" aria-labelledby="sponsorshipSupportOptionsModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sponsorshipSupportOptionsModalLabel">Ways to support</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">
                    These options appear on each mother’s (and other sponsorship) profile. Donors click one to open a commitment form for that option only.
                </p>
                <form action="{{ route('sponsorship.supportOptions', [], false) }}" method="POST" data-turbo="false">
                    @csrf
                    <div id="supportOptionsEditor" class="d-flex flex-column gap-3">
                        @foreach($editableSupportOptions as $index => $option)
                            <div class="border rounded p-3 bg-light support-option-row">
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label">Label</label>
                                        <input type="text" class="form-control" name="support_options[{{ $index }}][label]" value="{{ $option['label'] ?? '' }}" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Key</label>
                                        <input type="text" class="form-control" name="support_options[{{ $index }}][key]" value="{{ $option['key'] ?? '' }}" placeholder="auto from label">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Icon</label>
                                        <input type="text" class="form-control" name="support_options[{{ $index }}][icon]" value="{{ $option['icon'] ?? 'fa-heart' }}" placeholder="fa-heart">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Sort</label>
                                        <input type="number" class="form-control" name="support_options[{{ $index }}][sort]" value="{{ $option['sort'] ?? (($index + 1) * 10) }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Short description</label>
                                        <input type="text" class="form-control" name="support_options[{{ $index }}][text]" value="{{ $option['text'] ?? '' }}">
                                    </div>
                                    <div class="col-12 d-flex align-items-center justify-content-between">
                                        <div class="form-check mb-0">
                                            <input type="hidden" name="support_options[{{ $index }}][active]" value="0">
                                            <input
                                                type="checkbox"
                                                class="form-check-input"
                                                id="support_option_active_{{ $index }}"
                                                name="support_options[{{ $index }}][active]"
                                                value="1"
                                                {{ !empty($option['active']) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="support_option_active_{{ $index }}">Show on public profiles</label>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger js-remove-support-option">Remove</button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-between mt-3">
                        <button type="button" class="btn btn-outline-secondary" id="addSupportOptionBtn">Add option</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save ways to support</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<template id="supportOptionRowTemplate">
    <div class="border rounded p-3 bg-light support-option-row">
        <div class="row g-2">
            <div class="col-md-5">
                <label class="form-label">Label</label>
                <input type="text" class="form-control" name="support_options[__INDEX__][label]" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Key</label>
                <input type="text" class="form-control" name="support_options[__INDEX__][key]" placeholder="auto from label">
            </div>
            <div class="col-md-2">
                <label class="form-label">Icon</label>
                <input type="text" class="form-control" name="support_options[__INDEX__][icon]" value="fa-heart" placeholder="fa-heart">
            </div>
            <div class="col-md-2">
                <label class="form-label">Sort</label>
                <input type="number" class="form-control" name="support_options[__INDEX__][sort]" value="100">
            </div>
            <div class="col-12">
                <label class="form-label">Short description</label>
                <input type="text" class="form-control" name="support_options[__INDEX__][text]">
            </div>
            <div class="col-12 d-flex align-items-center justify-content-between">
                <div class="form-check mb-0">
                    <input type="hidden" name="support_options[__INDEX__][active]" value="0">
                    <input type="checkbox" class="form-check-input" id="support_option_active___INDEX__" name="support_options[__INDEX__][active]" value="1" checked>
                    <label class="form-check-label" for="support_option_active___INDEX__">Show on public profiles</label>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger js-remove-support-option">Remove</button>
            </div>
        </div>
    </div>
</template>
@endsection

@section('scripts')
    <script src="{{ asset('assets') }}/js/summernote.js"></script>
    <script>
        (function () {
            var editor = document.getElementById('supportOptionsEditor');
            var addBtn = document.getElementById('addSupportOptionBtn');
            var template = document.getElementById('supportOptionRowTemplate');
            if (!editor || !addBtn || !template) {
                return;
            }

            function nextIndex() {
                return editor.querySelectorAll('.support-option-row').length;
            }

            addBtn.addEventListener('click', function () {
                var html = template.innerHTML.replace(/__INDEX__/g, String(nextIndex()));
                editor.insertAdjacentHTML('beforeend', html);
            });

            editor.addEventListener('click', function (event) {
                var btn = event.target.closest('.js-remove-support-option');
                if (!btn) {
                    return;
                }
                var row = btn.closest('.support-option-row');
                if (row && editor.querySelectorAll('.support-option-row').length > 1) {
                    row.remove();
                }
            });
        })();
    </script>
    @if($errors->any() && old('form_intent') === 'create')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalEl = document.getElementById('sponsorshipCreateModal');
                if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            });
        </script>
    @elseif($errors->any() && str_starts_with((string) old('form_intent'), 'edit-'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var intent = @json(old('form_intent'));
                var id = String(intent || '').replace(/^edit-/, '');
                var modalEl = document.getElementById('sponsorshipEditModal' + id);
                if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            });
        </script>
    @endif
@endsection
