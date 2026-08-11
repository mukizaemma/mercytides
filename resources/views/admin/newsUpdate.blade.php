@extends('layouts.adminbase')

@section('title', 'Edit Update')

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
                        <h1>Edit Update</h1>
                        <p class="text-muted mb-0">Refine the story and photos, then publish when ready.</p>
                    </div>
                    <a href="{{ route('blog.index') }}" class="btn btn-outline-primary">Back to Updates</a>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('updateBlog', $blog->id) }}" method="POST" enctype="multipart/form-data" data-turbo="false">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-8">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title" value="{{ $blog->title }}" required>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label">Author</label>
                                    <input type="text" name="author" class="form-control" value="{{ $blog->author }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Story</label>
                                    <textarea class="form-control" rows="10" name="body" data-editor="rich">{!! $blog->body !!}</textarea>
                                </div>
                                <div class="col-12">
                                    <x-admin.image-field
                                        label="Cover image"
                                        name="image"
                                        library-name="image_path"
                                        :current="$blog->image"
                                        help="Replace the cover by uploading a new image or choosing one from the library."
                                    />
                                </div>
                                <div class="col-12">
                                    <x-admin.image-field
                                        label="Add more activity photos"
                                        name="gallery[]"
                                        library-name="gallery_paths"
                                        :multiple="true"
                                        help="Upload new photos or select existing ones to append to this update’s gallery."
                                    />
                                </div>
                            </div>
                            <div class="mt-4 d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                @if(!$blog->published_at)
                                    <form action="{{ route('publishBlog', $blog->id) }}" method="POST" data-turbo="false">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success">Publish</button>
                                    </form>
                                @else
                                    <form action="{{ route('unpublishBlog', $blog->id) }}" method="POST" data-turbo="false">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-secondary">Move to Draft</button>
                                    </form>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Activity photos ({{ $blog->blogimages->count() }})</div>
                    <div class="card-body">
                        @if($blog->blogimages->isEmpty())
                            <div class="admin-empty-state py-4">
                                <i class="fas fa-images d-block"></i>
                                <p class="mb-0">No activity photos yet.</p>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach($blog->blogimages as $image)
                                    <div class="col-md-3 col-sm-6">
                                        <div class="border rounded p-2 h-100">
                                            <img src="{{ asset('storage/' . ltrim($image->gallery, '/')) }}" class="img-fluid rounded mb-2" alt="Activity photo">
                                            <x-admin.delete-button :action="route('deleteBlogImage', $image->id)" confirm="Delete this photo?" class="btn btn-outline-danger btn-sm w-100" label="Delete photo" />
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
@endsection
