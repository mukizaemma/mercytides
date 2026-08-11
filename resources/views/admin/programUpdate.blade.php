@extends('layouts.adminbase')

@section('title', 'Edit Program')

@section('sidebar')
    @parent
@endsection

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        @include('admin.includes.sidenav')
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <div class="admin-page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h1>Edit Program</h1>
                        <p class="text-muted mb-0">Update program details and manage program gallery.</p>
                    </div>
                    <a href="{{ route('programs') }}" class="btn btn-outline-primary">Back to Programs</a>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('updateProgram', $data->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-8">
                                    <label class="form-label">Program title</label>
                                    <input type="text" class="form-control" name="title" value="{{ $data->title }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea rows="6" class="form-control" name="description" data-editor="rich">{!! $data->description !!}</textarea>
                                </div>
                                <div class="col-12">
                                    <x-admin.image-field
                                        label="Cover image"
                                        name="image"
                                        :current="$data->image ?? null"
                                        legacy-dir="images/programs"
                                    />
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary px-4">Save Program Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span>Program Gallery ({{ $totalImages }})</span>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#programImageModal">
                            <i class="fa fa-plus me-1"></i> Add Images
                        </button>
                    </div>
                    <div class="card-body">
                        @if($images->isEmpty())
                            <div class="admin-empty-state py-4">
                                <i class="fas fa-images d-block"></i>
                                <p class="mb-0">No gallery images added yet.</p>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach($images as $image)
                                    <div class="col-md-3 col-sm-6">
                                        <div class="border rounded p-2 h-100">
                                            <img src="{{ asset('storage/' . $image->image) }}" class="img-fluid rounded mb-2" alt="Program image">
                                            <form action="{{ route('deleteProgramImage', $image->id) }}" method="POST" onsubmit="return confirm('Delete this image?')">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100">Delete image</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>

<div class="modal fade" id="programImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Program Gallery Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('addProgramImage') }}" method="POST" enctype="multipart/form-data" data-turbo="false">
                    @csrf
                    <input type="hidden" name="program_id" value="{{ $data->id }}">
                    <div class="mb-3">
                        <x-admin.image-field
                            label="Select one or more images"
                            name="gallery_images[]"
                            :multiple="true"
                            legacy-dir="images/programs/gallery"
                            :required="true"
                        />
                    </div>
                    <button type="submit" class="btn btn-primary">Upload Images</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
