@extends('layouts.adminbase')

@section('title', 'Edit impact metric')

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
            <div class="container-fluid px-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('impacts.index') }}">Impact metrics</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                    <a href="{{ route('impacts.index') }}" class="btn btn-outline-secondary">Back</a>
                </div>

                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session()->get('success') }}</div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <form action="{{ route('updateImpact', $data->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label" for="impact_value">Value <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('value') is-invalid @enderror"
                                        id="impact_value" name="value" value="{{ old('value', $data->value) }}" required>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label" for="title">Label <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                        id="title" name="title" value="{{ old('title', $data->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="sort_order">Display order</label>
                                    <input type="number" min="0" class="form-control @error('sort_order') is-invalid @enderror"
                                        id="sort_order" name="sort_order" value="{{ old('sort_order', $data->sort_order ?? 0) }}">
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label" for="status">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="Active" @selected(old('status', $data->status) === 'Active')>Active</option>
                                        <option value="Inactive" @selected(old('status', $data->status) === 'Inactive')>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="ProgramDescription">Optional note</label>
                                    <textarea id="ProgramDescription" rows="3"
                                        class="form-control @error('description') is-invalid @enderror"
                                        name="description">{{ old('description', $data->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Current image</label>
                                    <div>
                                        @if(!empty($data->image))
                                            <img src="{{ asset('storage/images/impacts/' . $data->image) }}" alt="" width="120">
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="image">Replace image (optional)</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror"
                                        id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save changes
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
