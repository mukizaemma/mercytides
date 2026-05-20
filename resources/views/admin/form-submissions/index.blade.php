@extends('layouts.adminbase')

@section('title', 'Form submissions')

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">@include('admin.includes.sidenav')</div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <div class="admin-page-header">
                    <h1>Form submissions</h1>
                    <p class="text-muted mb-0">Guest inquiries saved when they chose WhatsApp or Email. Channel totals show which option people prefer.</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Total submissions</p>
                                <p class="h3 mb-0">{{ number_format($stats['total']) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Via WhatsApp</p>
                                <p class="h3 mb-0 text-success">{{ number_format($stats['by_channel']['whatsapp'] ?? 0) }}</p>
                                @if($stats['total'] > 0)
                                    <p class="small text-muted mb-0">{{ round(100 * ($stats['by_channel']['whatsapp'] ?? 0) / $stats['total']) }}% of all</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Via Email</p>
                                <p class="h3 mb-0">{{ number_format($stats['by_channel']['email'] ?? 0) }}</p>
                                @if($stats['total'] > 0)
                                    <p class="small text-muted mb-0">{{ round(100 * ($stats['by_channel']['email'] ?? 0) / $stats['total']) }}% of all</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if(count($stats['by_form']) > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h2 class="h6 mb-3">By form type</h2>
                            <div class="table-responsive">
                                <table class="table table-sm mb-0">
                                    <thead><tr><th>Form</th><th class="text-end">Submissions</th></tr></thead>
                                    <tbody>
                                        @foreach($stats['by_form'] as $type => $count)
                                            <tr>
                                                <td>{{ $formTypes[$type] ?? $type }}</td>
                                                <td class="text-end">{{ $count }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <form method="get" class="row g-2 align-items-end mb-3">
                            <div class="col-md-4">
                                <label class="form-label small">Form type</label>
                                <select name="form_type" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    @foreach($formTypes as $key => $label)
                                        <option value="{{ $key }}" @selected(request('form_type') === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Channel</label>
                                <select name="channel" class="form-select form-select-sm">
                                    <option value="">All</option>
                                    <option value="whatsapp" @selected(request('channel') === 'whatsapp')>WhatsApp</option>
                                    <option value="email" @selected(request('channel') === 'email')>Email</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-sm btn-secondary">Filter</button>
                                <a href="{{ route('formSubmissions.admin') }}" class="btn btn-sm btn-link">Clear</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>When</th>
                                        <th>Form</th>
                                        <th>Channel</th>
                                        <th>Name</th>
                                        <th>Contact</th>
                                        <th>Preview</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($submissions as $row)
                                        <tr>
                                            <td class="small text-nowrap">{{ $row->created_at->format('M j, Y H:i') }}</td>
                                            <td>{{ $row->formTypeLabel() }}</td>
                                            <td>
                                                <span class="badge {{ $row->channel === 'whatsapp' ? 'bg-success' : 'bg-dark' }}">
                                                    {{ $row->channelLabel() }}
                                                </span>
                                            </td>
                                            <td>{{ $row->submitter_name ?? '—' }}</td>
                                            <td class="small">
                                                @if($row->submitter_email)<div>{{ $row->submitter_email }}</div>@endif
                                                @if($row->submitter_phone)<div>{{ $row->submitter_phone }}</div>@endif
                                            </td>
                                            <td class="small text-muted">
                                                @if($row->form_type === 'donate' && !empty($row->payload['payment_method']))
                                                    <span class="badge bg-light text-dark border mb-1">{{ \App\Support\DonationPaymentMethods::label($row->payload['payment_method']) }}</span><br>
                                                @endif
                                                {{ \Illuminate\Support\Str::limit($row->message_preview, 80) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-muted text-center py-4">No submissions yet.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">{{ $submissions->links() }}</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
