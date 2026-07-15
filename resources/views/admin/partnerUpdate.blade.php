@extends('layouts.adminbase')

@section('title', 'Edit partner')

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
                        <h1>Edit partner</h1>
                        <p class="text-muted mb-0">{{ $data->names }}</p>
                    </div>
                    <a href="{{ route('partner') }}" class="btn btn-outline-secondary">Back</a>
                </div>

                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session()->get('success') }}</div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session()->get('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('updatePartner', $data->id, false) }}" method="POST" enctype="multipart/form-data" data-turbo="false">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="names" required value="{{ old('names', $data->names) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Website</label>
                                    <input type="text" class="form-control" name="website" value="{{ old('website', $data->website) }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="4" data-editor="rich">{{ old('description', $data->description) }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Current logo</label>
                                    <div>
                                        @if($data->hasLogo())
                                            <img
                                                src="{{ $data->logoUrl() }}"
                                                alt="{{ $data->names }}"
                                                class="rounded border bg-white p-2"
                                                style="max-height: 120px; width: auto; object-fit: contain;"
                                            >
                                        @else
                                            <span class="text-muted">No logo uploaded</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Replace logo</label>
                                    <input type="file" class="form-control" name="image" accept="image/jpeg,image/png,image/gif,image/webp,.jpg,.jpeg,.png,.gif,.webp" data-image-preset="logo">
                                    <small class="text-muted">Leave blank to keep the current logo. JPG, PNG, GIF, or WEBP.</small>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('partner') }}" class="btn btn-outline-secondary">Cancel</a>
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

@section('scripts')
    <script src="{{ asset('assets') }}/js/summernote.js"></script>
@endsection
