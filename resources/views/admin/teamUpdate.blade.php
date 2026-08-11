@extends('layouts.adminbase')

@section('title', 'Edit Team Member')

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
                        <h1>Edit team member</h1>
                        <p class="text-muted mb-0">Update leadership profile details.</p>
                    </div>
                    <a href="{{ route('staff') }}" class="btn btn-outline-primary" data-turbo="false">Back to team</a>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <form class="form" action="{{ route('updateStaff', $data->id) }}" method="POST" enctype="multipart/form-data" data-turbo="false">
                            @csrf
                            <div class="row g-3">
                                <div class="col-lg-8">
                                    <label class="form-label" for="names">Names</label>
                                    <input type="text" class="form-control" id="names" value="{{ $data->names }}" name="names" required>
                                </div>
                                <div class="col-lg-4">
                                    <label class="form-label" for="position">Position</label>
                                    <input type="text" class="form-control" id="position" value="{{ $data->position }}" name="position" required>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label" for="phone">Phone</label>
                                    <input type="text" class="form-control" id="phone" value="{{ $data->phone }}" name="phone">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label" for="facebook">Email</label>
                                    <input type="text" class="form-control" id="facebook" value="{{ $data->facebook }}" name="facebook">
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Current photo</label>
                                    <div>
                                        @if(!empty($data->image))
                                            <img src="{{ asset('storage/images/staff' . $data->image) }}" alt="{{ $data->names }}" width="120" class="rounded border">
                                        @else
                                            <span class="text-muted">No photo</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label" for="image">Change photo</label>
                                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="bio">Biography</label>
                                    <textarea id="bio" rows="8" class="form-control" name="bio" data-editor="rich">{!! $data->bio !!}</textarea>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save me-1"></i> Save Changes
                                </button>
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
