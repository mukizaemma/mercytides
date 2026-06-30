@extends('layouts.adminbase')

@section('title', 'Home Slides')

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
                        <h1>Home slides</h1>
                        <p class="text-muted mb-0">Hero images and headlines for the public homepage.</p>
                    </div>
                    <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#slideCreateModal">
                        <i class="fa fa-plus me-1"></i> Add slide
                    </button>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive admin-table-wrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Caption</th>
                                        <th>Preview</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($slides as $rs)
                                        <tr>
                                            <td class="fw-semibold">{{ $rs->heading ?: '—' }}</td>
                                            <td>
                                                <img src="{{ \App\Models\Slide::publicImageUrl($rs->image) }}" alt="" class="rounded border" width="120">
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#slideEditModal{{ $rs->id }}">Edit</button>
                                                    <a href="{{ route('destroySlide', $rs->id) }}" class="btn btn-outline-danger" onclick="return confirm('Delete this slide?')">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="border-0">
                                                <div class="admin-empty-state">
                                                    <p class="mb-0">No slides yet. Add your first hero image.</p>
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

<div class="modal fade" id="slideCreateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add slide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('saveSlide') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Headline</label>
                        <input type="text" class="form-control" name="heading" placeholder="Breaking Barriers, Bridging A Better Future">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hero image <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="image" required accept="image/*" data-image-preset="hero">
                        <small class="text-muted">Landscape photo of mothers/children works best (wide format).</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Save slide</button>
                </form>
            </div>
        </div>
    </div>
</div>

@foreach($slides as $rs)
<div class="modal fade" id="slideEditModal{{ $rs->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit slide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('updateSlide', $rs->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Headline</label>
                        <input type="text" class="form-control" name="heading" value="{{ $rs->heading }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Replace image (optional)</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <img src="{{ \App\Models\Slide::publicImageUrl($rs->image) }}" alt="" class="mt-2 rounded border" width="160">
                    </div>
                    <button type="submit" class="btn btn-primary">Update slide</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection
