@extends('layouts.adminbase')

@section('title', 'Young Mothers')

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
                        <h1>Young mothers</h1>
                        <p class="text-muted mb-0">Portrait gallery for the homepage and impact page. Only the photo is required — add names, age, testimonial, and vision when available.</p>
                    </div>
                    <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#motherCreateModal">
                        <i class="fa fa-plus me-1"></i> Add mother
                    </button>
                </div>

                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session()->get('success') }}</div>
                @endif
                @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session()->get('error') }}</div>
                @endif

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive admin-table-wrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Portrait</th>
                                        <th>Name</th>
                                        <th>Age</th>
                                        <th>Details</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($mothers as $mother)
                                        <tr>
                                            <td>
                                                <img src="{{ \App\Models\Mother::publicImageUrl($mother->image) }}" alt="{{ $mother->displayName() }}" class="rounded border" width="72" height="96" style="object-fit:cover;">
                                            </td>
                                            <td class="fw-semibold">{{ $mother->name ?: '—' }}</td>
                                            <td>{{ $mother->age ?: '—' }}</td>
                                            <td>
                                                @if($mother->hasProfileDetails())
                                                    <span class="badge bg-success">Profile ready</span>
                                                @else
                                                    <span class="badge bg-secondary">Photo only</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#motherEditModal{{ $mother->id }}">Edit</button>
                                                    <a href="{{ route('destroyMother', $mother->id) }}" class="btn btn-outline-danger" onclick="return confirm('Delete this mother profile?')">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="border-0">
                                                <div class="admin-empty-state">
                                                    <p class="mb-0">No mothers yet. Upload portrait photos to populate the public gallery.</p>
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

<div class="modal fade" id="motherCreateModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add young mother</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('saveMother', [], false) }}" method="POST" enctype="multipart/form-data" data-turbo="false" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Portrait photo <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="image" required accept="image/*" data-image-preset="portrait">
                        <small class="text-muted">Vertical portrait photos work best for the gallery. You’ll see the estimated size after selecting a file.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Optional for now">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Age</label>
                            <input type="text" class="form-control" name="age" placeholder="Optional">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Testimonial / story</label>
                        <textarea class="form-control" name="description" rows="4" data-editor="rich" placeholder="Her story in her own words (optional)"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vision</label>
                        <textarea class="form-control" name="vision" rows="3" data-editor="rich" placeholder="Her hopes and vision for the future (optional)"></textarea>
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-end pt-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@foreach($mothers as $mother)
<div class="modal fade" id="motherEditModal{{ $mother->id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit young mother</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('updateMother', $mother->id, false) }}" method="POST" enctype="multipart/form-data" data-turbo="false" autocomplete="off">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Replace portrait (optional)</label>
                        <input type="file" class="form-control" name="image" accept="image/*" data-image-preset="portrait">
                        <img src="{{ \App\Models\Mother::publicImageUrl($mother->image) }}" alt="" class="mt-2 rounded border" width="120" style="aspect-ratio:3/4;object-fit:cover;">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $mother->name }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Age</label>
                            <input type="text" class="form-control" name="age" value="{{ $mother->age }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Testimonial / story</label>
                        <textarea class="form-control" name="description" rows="4" data-editor="rich">{!! $mother->description !!}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vision</label>
                        <textarea class="form-control" name="vision" rows="3" data-editor="rich">{!! $mother->vision !!}</textarea>
                    </div>
                    <div class="d-flex flex-wrap gap-2 justify-content-end pt-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

@section('scripts')
    <script src="{{ asset('assets') }}/js/summernote.js"></script>
@endsection
