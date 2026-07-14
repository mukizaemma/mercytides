@extends('layouts.adminbase')

@section('title', 'Page headers')

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
                        <h1>Page headers</h1>
                        <p class="text-muted mb-0">Upload a hero image for each public page. Pages without a specific image use the site default.</p>
                    </div>
                    <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#pageHeaderCreateModal">
                        <i class="fa fa-plus me-1"></i> Add custom header
                    </button>
                </div>

                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session()->get('success') }}</div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session()->get('error') }}</div>
                @endif

                <div class="card mb-3">
                    <div class="card-body d-flex flex-wrap align-items-center gap-3">
                        <div>
                            <div class="text-uppercase small text-muted fw-semibold">Current site default</div>
                            <div class="fw-semibold">{{ $default?->label ?? 'Not set yet' }}</div>
                            <small class="text-muted">Used when a page has no image of its own.</small>
                        </div>
                        @if($default?->imageUrl())
                            <img src="{{ $default->imageUrl() }}" alt="" class="rounded border ms-auto" width="160" height="70" style="object-fit:cover;">
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive admin-table-wrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Page</th>
                                        <th>Key</th>
                                        <th>Preview</th>
                                        <th>Default</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($headers as $header)
                                        <tr>
                                            <td class="fw-semibold">{{ $header->label }}</td>
                                            <td><code class="small">{{ $header->page_key }}</code></td>
                                            <td>
                                                @if($header->imageUrl())
                                                    <img src="{{ $header->imageUrl() }}" alt="" class="rounded border" width="140" height="60" style="object-fit:cover;">
                                                @else
                                                    <span class="text-muted small">Uses site default</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($header->is_default)
                                                    <span class="badge bg-primary">Default</span>
                                                @elseif($header->imageUrl())
                                                    <a href="{{ route('pageHeaders.setDefault', $header->id) }}" class="btn btn-sm btn-outline-secondary">Make default</a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#pageHeaderEditModal{{ $header->id }}">Edit</button>
                                                    @if($header->imageUrl())
                                                        <a href="{{ route('pageHeaders.clear', $header->id) }}" class="btn btn-outline-warning" onclick="return confirm('Clear this page image and fall back to the site default?')">Clear</a>
                                                    @endif
                                                    @unless(array_key_exists($header->page_key, \App\Models\PageHeader::catalog()))
                                                        <a href="{{ route('pageHeaders.destroy', $header->id) }}" class="btn btn-outline-danger" onclick="return confirm('Delete this custom page header?')">Delete</a>
                                                    @endunless
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
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

@include('admin.includes.admin-modal', [
    'id' => 'pageHeaderCreateModal',
    'title' => 'Add custom page header',
    'body' => view('admin.includes.page-header-form-fields')->render(),
])

@foreach($headers as $header)
    @include('admin.includes.admin-modal', [
        'id' => 'pageHeaderEditModal'.$header->id,
        'title' => 'Edit '.$header->label,
        'body' => view('admin.includes.page-header-form-fields', ['header' => $header])->render(),
    ])
@endforeach
@endsection
