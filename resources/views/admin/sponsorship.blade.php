@extends('layouts.adminbase')

@section('title', 'Sponsorship profiles')

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
                        <h1>Sponsorship profiles</h1>
                        <p class="text-muted mb-0">Manage children, young mothers, and families available for sponsorship. Each profile appears on its category page with story, challenges, video, and a donor inquiry form.</p>
                    </div>
                    <button class="btn btn-primary px-3" data-bs-toggle="modal" data-bs-target="#sponsorshipCreateModal">
                        <i class="fa fa-plus me-1"></i> Add profile
                    </button>
                </div>

                @if (session()->has('success'))
                    <div class="alert alert-success">{{ session()->get('success') }}</div>
                @endif

                <div class="card mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('sponsorship.index') }}" class="btn btn-sm {{ empty($typeFilter) ? 'btn-primary' : 'btn-outline-secondary' }}">All types</a>
                            @foreach($types as $key => $meta)
                                <a href="{{ route('sponsorship.index', ['type' => $key]) }}" class="btn btn-sm {{ ($typeFilter ?? '') === $key ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $meta['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive admin-table-wrap">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Monthly need</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($profiles as $profile)
                                        <tr>
                                            <td>
                                                <img src="{{ \App\Models\Sponsorship::publicImageUrl($profile->image) }}" alt="{{ $profile->displayName() }}" class="rounded border" width="72" height="96" style="object-fit:cover;">
                                            </td>
                                            <td>
                                                <div class="fw-semibold">{{ $profile->displayName() }}</div>
                                                <small class="text-muted">{{ $profile->age ? 'Age '.$profile->age : '—' }}</small>
                                            </td>
                                            <td><span class="badge bg-light text-dark border">{{ $profile->typeLabel() }}</span></td>
                                            <td>
                                                <span class="badge {{ $profile->isAvailable() ? 'bg-warning text-dark' : 'bg-success' }}">{{ $profile->status }}</span>
                                                @if(!$profile->isPublished())
                                                    <span class="badge bg-secondary">Draft</span>
                                                @endif
                                            </td>
                                            <td>{{ $profile->monthly_need ? '$'.$profile->monthly_need : '—' }}</td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ $profile->profileRoute() }}" class="btn btn-outline-secondary" target="_blank" rel="noopener">View</a>
                                                    <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sponsorshipEditModal{{ $profile->id }}">Edit</button>
                                                    <a href="{{ route('destroySponsorship', $profile->id) }}" class="btn btn-outline-danger" onclick="return confirm('Delete this sponsorship profile?')">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="border-0">
                                                <div class="admin-empty-state">
                                                    <p class="mb-0">No sponsorship profiles yet. Add a child, young mother, or family to publish on the website.</p>
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

@include('admin.includes.admin-modal', [
    'id' => 'sponsorshipCreateModal',
    'title' => 'Add sponsorship profile',
    'body' => view('admin.includes.sponsorship-form-fields', [
        'formProfile' => null,
        'types' => $types,
    ])->render(),
])

@foreach($profiles as $profile)
    @include('admin.includes.admin-modal', [
        'id' => 'sponsorshipEditModal'.$profile->id,
        'title' => 'Edit '.$profile->displayName(),
        'body' => view('admin.includes.sponsorship-form-fields', [
            'formProfile' => $profile,
            'types' => $types,
        ])->render(),
    ])
@endforeach
@endsection

@section('scripts')
    <script src="{{ asset('assets') }}/js/summernote.js"></script>
@endsection
