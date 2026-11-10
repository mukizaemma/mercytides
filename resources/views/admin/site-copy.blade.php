@extends('layouts.adminbase')

@section('title', 'Page copy')

@section('content')
@php
    $active = $active ?? 'objectives';
    $objectiveRows = old('objectives', $objectives ?? []);
    if (! is_array($objectiveRows) || $objectiveRows === []) {
        $objectiveRows = \App\Support\MercyTidesContent::objectiveBlueprint();
    }
@endphp
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        @include('admin.includes.sidenav')
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <div class="admin-page-header">
                    <h1>Page copy</h1>
                    <p class="text-muted mb-0">Edit the fixed wording across the public site: daily objectives, homepage sections, page titles, menus, and introductions. Stories, people, programs, and photos stay in their own screens.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(!($ready ?? true))
                    <div class="alert alert-warning">Page copy cannot be saved because the database is not available.</div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-4 flex-nowrap overflow-auto" id="siteCopyTabs" role="tablist">
                            @foreach($groups as $groupId => $group)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link text-nowrap {{ $active === $groupId ? 'active' : '' }}" id="{{ $groupId }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $groupId }}-pane" type="button" role="tab">{{ $group['label'] }}</button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach($groups as $groupId => $group)
                                <div class="tab-pane fade {{ $active === $groupId ? 'show active' : '' }}" id="{{ $groupId }}-pane" role="tabpanel" aria-labelledby="{{ $groupId }}-tab">
                                    <form action="{{ route('siteCopy.update', ['tab' => $groupId]) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="group" value="{{ $groupId }}">
                                        <p class="text-muted">{{ $group['intro'] }}</p>
                                        @if(!empty($group['preview']))
                                            <p class="small"><a href="{{ route($group['preview']) }}" target="_blank" rel="noopener">Preview this area</a></p>
                                        @endif

                                        @php $fieldIndex = 0; @endphp
                                        @foreach($group['fields'] as $field)
                                            @if(($field['type'] ?? '') === 'heading')
                                                <h2 class="h6 text-uppercase text-muted mt-4 mb-3">{{ $field['label'] }}</h2>
                                                @continue
                                            @endif
                                            @php
                                                $key = $field['key'];
                                                $type = $field['type'] ?? 'text';
                                                $current = $type === 'lines'
                                                    ? \App\Support\SiteContent::linesText($key)
                                                    : ($type === 'html'
                                                        ? \App\Support\SiteContent::html($key, $field['default'])
                                                        : \App\Support\SiteContent::text($key, $field['default']));
                                                $value = old('fields.'.$fieldIndex.'.value', $current);
                                            @endphp
                                            <div class="mb-3">
                                                <label class="form-label" for="copy-{{ $groupId }}-{{ $fieldIndex }}">{{ $field['label'] }}</label>
                                                @if(!empty($field['help']))
                                                    <p class="text-muted small mb-2">{{ $field['help'] }}</p>
                                                @endif
                                                <input type="hidden" name="fields[{{ $fieldIndex }}][key]" value="{{ $key }}">
                                                @if($type === 'text')
                                                    <input id="copy-{{ $groupId }}-{{ $fieldIndex }}" type="text" class="form-control" name="fields[{{ $fieldIndex }}][value]" value="{{ $value }}">
                                                @else
                                                    <textarea
                                                        id="copy-{{ $groupId }}-{{ $fieldIndex }}"
                                                        class="form-control {{ $type === 'lines' ? 'font-monospace' : '' }}"
                                                        name="fields[{{ $fieldIndex }}][value]"
                                                        rows="{{ $type === 'html' ? 8 : ($type === 'lines' ? 5 : 3) }}"
                                                        data-editor="{{ $type === 'html' ? 'rich' : 'plain' }}"
                                                    >{{ $value }}</textarea>
                                                @endif
                                            </div>
                                            @php $fieldIndex++; @endphp
                                        @endforeach

                                        @if(!empty($group['objectives']))
                                            <h2 class="h6 text-uppercase text-muted mt-4 mb-3">Objective cards</h2>
                                            <p class="text-muted small">One card per row. Icons use Font Awesome classes such as <code>fas fa-graduation-cap</code>. Leave a row blank to remove it.</p>
                                            <div id="objectiveEditor" class="d-flex flex-column gap-3">
                                                @foreach($objectiveRows as $index => $item)
                                                    <div class="border rounded p-3 bg-light objective-row">
                                                        <div class="row g-2 align-items-end">
                                                            <div class="col-lg-4">
                                                                <label class="form-label">Icon</label>
                                                                <input type="text" class="form-control" name="objectives[{{ $index }}][icon]" value="{{ $item['icon'] ?? 'fas fa-check-circle' }}" list="objective-icons" placeholder="fas fa-heart">
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <label class="form-label">Text</label>
                                                                <input type="text" class="form-control" name="objectives[{{ $index }}][text]" value="{{ $item['text'] ?? '' }}">
                                                            </div>
                                                            <div class="col-lg-2 text-lg-end">
                                                                <button type="button" class="btn btn-outline-danger js-remove-objective">Remove</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button type="button" class="btn btn-outline-secondary mt-3" id="addObjectiveBtn">Add objective</button>
                                        @endif

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary" @disabled(!($ready ?? true))>
                                                <i class="fa fa-save me-1"></i> Save {{ strtolower($group['label']) }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>

<datalist id="objective-icons">
    <option value="fas fa-graduation-cap"></option>
    <option value="fas fa-handshake"></option>
    <option value="fas fa-coins"></option>
    <option value="fas fa-store"></option>
    <option value="fas fa-users"></option>
    <option value="fas fa-solar-panel"></option>
    <option value="fas fa-heart"></option>
    <option value="fas fa-hands-helping"></option>
    <option value="fas fa-book-open"></option>
    <option value="fas fa-child"></option>
    <option value="fas fa-home"></option>
    <option value="fas fa-pray"></option>
    <option value="fas fa-seedling"></option>
    <option value="fas fa-briefcase"></option>
    <option value="fas fa-church"></option>
    <option value="fas fa-star"></option>
    <option value="fas fa-check-circle"></option>
</datalist>

<template id="objectiveRowTemplate">
    <div class="border rounded p-3 bg-light objective-row">
        <div class="row g-2 align-items-end">
            <div class="col-lg-4">
                <label class="form-label">Icon</label>
                <input type="text" class="form-control" name="objectives[__INDEX__][icon]" value="fas fa-check-circle" list="objective-icons" placeholder="fas fa-heart">
            </div>
            <div class="col-lg-6">
                <label class="form-label">Text</label>
                <input type="text" class="form-control" name="objectives[__INDEX__][text]" value="">
            </div>
            <div class="col-lg-2 text-lg-end">
                <button type="button" class="btn btn-outline-danger js-remove-objective">Remove</button>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
    (function () {
        var editor = document.getElementById('objectiveEditor');
        var addBtn = document.getElementById('addObjectiveBtn');
        var template = document.getElementById('objectiveRowTemplate');
        if (!editor || !addBtn || !template) {
            return;
        }

        addBtn.addEventListener('click', function () {
            var html = template.innerHTML.replace(/__INDEX__/g, String(Date.now()));
            editor.insertAdjacentHTML('beforeend', html);
        });

        editor.addEventListener('click', function (event) {
            var btn = event.target.closest('.js-remove-objective');
            if (!btn) {
                return;
            }
            var row = btn.closest('.objective-row');
            if (row && editor.querySelectorAll('.objective-row').length > 1) {
                row.remove();
            }
        });
    })();
</script>
@endpush
