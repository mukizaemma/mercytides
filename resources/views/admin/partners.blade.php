@extends('layouts.adminbase')

@section('title', 'Partners')

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
                        <h1>Partners</h1>
                        <p class="text-muted mb-0">Partner and church logos shown on the homepage.</p>
                    </div>
                    <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#partnerCreateModal">
                        <i class="fa fa-plus me-1"></i> Add partner
                    </button>
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
                    <div class="card-body p-0">
                        <div class="table-responsive admin-table-wrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Logo</th>
                                        <th>Name</th>
                                        <th>Website</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($data as $rs)
                                        <tr>
                                            <td>
                                                @if($rs->hasLogo())
                                                    <img
                                                        src="{{ $rs->logoUrl() }}"
                                                        alt="{{ $rs->names }}"
                                                        class="rounded border bg-white p-1"
                                                        style="max-height: 64px; width: auto; object-fit: contain;"
                                                    >
                                                @else
                                                    <span class="text-muted">No logo</span>
                                                @endif
                                            </td>
                                            <td class="fw-semibold">{{ $rs->names }}</td>
                                            <td>
                                                @if(!empty($rs->website))
                                                    <a href="{{ \Illuminate\Support\Str::startsWith($rs->website, ['http://', 'https://']) ? $rs->website : 'https://'.$rs->website }}" target="_blank" rel="noopener">{{ $rs->website }}</a>
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('editPartner', $rs->id) }}" class="btn btn-outline-primary">Edit</a>
                                                    <a href="{{ route('destroyPartner', $rs->id) }}" class="btn btn-outline-danger" onclick="return confirm('Delete this partner?')">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="border-0">
                                                <div class="admin-empty-state">
                                                    <p class="mb-0">No partners yet. Add a logo to show on the homepage.</p>
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

<div class="modal fade admin-modal" id="partnerCreateModal" tabindex="-1" aria-labelledby="partnerCreateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="partnerCreateModalLabel">Add partner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('savePartner', [], false) }}" method="POST" enctype="multipart/form-data" data-turbo="false">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="names" required value="{{ old('names') }}" placeholder="Partner or church name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Website</label>
                            <input type="text" class="form-control" name="website" value="{{ old('website') }}" placeholder="https://…">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="4" data-editor="rich" placeholder="Optional short note">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Logo <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="image" required accept="image/jpeg,image/png,image/gif,image/webp,.jpg,.jpeg,.png,.gif,.webp" data-image-preset="logo">
                            <small class="text-muted">JPG, PNG, GIF, or WEBP — max about 4MB.</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save partner</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('assets') }}/js/summernote.js"></script>
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalEl = document.getElementById('partnerCreateModal');
                if (modalEl && window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
                }
            });
        </script>
    @endif
@endsection
