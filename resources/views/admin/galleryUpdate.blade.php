@extends('layouts.adminbase')

@section('title', 'Edit gallery item')

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
                        <h1>Edit gallery item</h1>
                        <p class="text-muted mb-0">Update the image, YouTube link, or caption.</p>
                    </div>
                    <a href="{{ route('images') }}" class="btn btn-outline-secondary">Back to gallery</a>
                </div>

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
                    <div class="card-body">
                        <form action="{{ route('updateGallery', $data->id) }}" method="POST" enctype="multipart/form-data" data-turbo="false">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label d-block">Current preview</label>
                                    <img src="{{ $data->thumbUrl() }}" alt="" class="rounded border" width="220" style="max-width:100%; object-fit:cover; aspect-ratio:4/3;">
                                </div>
                                <div class="col-md-8">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label" for="edit_image">Replace image</label>
                                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="edit_youtube">YouTube URL</label>
                                            <input type="url" class="form-control" id="edit_youtube" name="youtube_url" value="{{ old('youtube_url', $data->youtube_url) }}" placeholder="https://www.youtube.com/watch?v=…">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="edit_program">Program (optional)</label>
                                            <select name="program_id" id="edit_program" class="form-select">
                                                <option value="">— None —</option>
                                                @foreach ($programs as $program)
                                                    <option value="{{ $program->id }}" @selected(old('program_id', $data->program_id) == $program->id)>{{ $program->title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="edit_caption">Caption</label>
                                            <input type="text" class="form-control" id="edit_caption" name="caption" value="{{ old('caption', $data->caption) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>

@endsection
