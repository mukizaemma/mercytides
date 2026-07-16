@extends('layouts.adminbase')

@section('title', 'Impact metrics')

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
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Impact metrics</li>
                </ol>

                @if(session()->has('success'))
                    <div class="alert alert-success">{{ session()->get('success') }}</div>
                @endif
                @if(session()->has('error'))
                    <div class="alert alert-danger">{{ session()->get('error') }}</div>
                @endif

                <div class="alert alert-light border mb-4">
                    These numbers appear on the home page, Impact page, and Get Involved page.
                    Add as many stats as you need — each item needs a <strong>label</strong> (title) and a <strong>value</strong>.
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Current stats</span>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
                            <i class="fa fa-plus"></i> Add metric
                        </button>
                    </div>
                    <div class="card-body">
                        @if($impacts->isEmpty())
                            <p class="text-muted mb-0">No impact metrics yet. Add your first one to replace the default site numbers.</p>
                        @else
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">Order</th>
                                        <th>Value</th>
                                        <th>Label</th>
                                        <th>Status</th>
                                        <th style="width: 180px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($impacts as $rs)
                                        <tr>
                                            <td>{{ $rs->sort_order }}</td>
                                            <td><strong>{{ $rs->value ?? '—' }}</strong></td>
                                            <td>{{ $rs->title }}</td>
                                            <td>
                                                @if($rs->isActive())
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('editImpact', $rs->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                                <a href="{{ route('destroyImpact', $rs->id) }}"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this impact metric?')">Delete</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>

                <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="addImpactLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="addImpactLabel">Add impact metric</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('saveImpact') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label" for="impact_value">Value <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('value') is-invalid @enderror"
                                                id="impact_value" name="value" value="{{ old('value') }}"
                                                placeholder="e.g. 6" required>
                                            @error('value')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label" for="title">Label <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                                id="title" name="title" value="{{ old('title') }}"
                                                placeholder="e.g. Mothers empowered" required>
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="sort_order">Display order</label>
                                            <input type="number" min="0" class="form-control @error('sort_order') is-invalid @enderror"
                                                id="sort_order" name="sort_order" value="{{ old('sort_order', $impacts->count()) }}">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">Lower numbers appear first.</div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label" for="status">Status</label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                                <option value="Active" @selected(old('status', 'Active') === 'Active')>Active</option>
                                                <option value="Inactive" @selected(old('status') === 'Inactive')>Inactive</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label" for="ProgramDescription">Optional note</label>
                                            <textarea id="ProgramDescription" rows="3"
                                                class="form-control @error('description') is-invalid @enderror"
                                                name="description">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="image">Optional image</label>
                                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                                id="image" name="image" accept="image/*">
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-save"></i> Save metric
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>

@endsection
