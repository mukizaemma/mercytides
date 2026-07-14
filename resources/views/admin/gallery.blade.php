@extends('layouts.adminbase')

@section('title', 'Media gallery')

@section('content')

<div id="layoutSidenav">
    <div id="layoutSidenav_nav" data-turbo-permanent>
        @include('admin.includes.sidenav')
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h1>Media gallery</h1>
                        <p class="text-muted mb-0">Photos and YouTube videos shown on the public Gallery page (16 per page).</p>
                    </div>
                    <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#galleryCreateModal">
                        <i class="fa fa-plus me-1"></i> Add item
                    </button>
                </div>

                @if(session()->has('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session()->has('warning'))
                    <div class="alert alert-warning">{{ session('warning') }}</div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Preview</th>
                                        <th>Type</th>
                                        <th>Caption</th>
                                        <th>Program</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($images as $item)
                                        <tr>
                                            <td>
                                                <img src="{{ $item->thumbUrl() }}" alt="" width="96" height="64" class="rounded border" style="object-fit:cover;">
                                            </td>
                                            <td>
                                                @if($item->isVideo())
                                                    <span class="badge text-bg-danger">YouTube</span>
                                                @else
                                                    <span class="badge text-bg-secondary">Image</span>
                                                @endif
                                            </td>
                                            <td>{{ $item->caption ?: '—' }}</td>
                                            <td>{{ $item->program->title ?? '—' }}</td>
                                            <td class="text-end">
                                                <div class="btn-group">
                                                    <a href="{{ route('editGallery', $item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                    <a href="{{ route('destroyGallery', $item->id) }}" class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Delete this gallery item?')">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">No gallery items yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="galleryCreateModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add gallery item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('saveGallery') }}" method="POST" enctype="multipart/form-data" data-turbo="false">
                                @csrf
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="gallery_image">Image file</label>
                                            <input type="file" class="form-control" id="gallery_image" name="image" accept="image/*">
                                            <small class="text-muted">Required unless you add a YouTube URL.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="gallery_youtube">YouTube URL</label>
                                            <input type="url" class="form-control" id="gallery_youtube" name="youtube_url" value="{{ old('youtube_url') }}" placeholder="https://www.youtube.com/watch?v=…">
                                            <small class="text-muted">Optional custom cover: upload an image above; otherwise the YouTube thumbnail is used.</small>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="gallery_program">Program (optional)</label>
                                            <select name="program_id" id="gallery_program" class="form-select">
                                                <option value="">— None —</option>
                                                @foreach ($programs as $program)
                                                    <option value="{{ $program->id }}" @selected(old('program_id') == $program->id)>{{ $program->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="gallery_caption">Caption</label>
                                            <input type="text" class="form-control" id="gallery_caption" name="caption" value="{{ old('caption') }}" placeholder="Short caption">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save item</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>

@endsection
