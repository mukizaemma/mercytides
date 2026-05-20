@extends('layouts.adminbase')

@section('title', 'Dashboard')

@section('content')
<div id="layoutSidenav">
    <div id="layoutSidenav_nav" data-turbo-permanent>
        @include('admin.includes.sidenav')
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 py-4">
                <div class="admin-page-header">
                    <h1>Dashboard</h1>
                    <p class="text-muted mb-0">Mercy Tides Foundation — overview of programs, giving, and applications.</p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="card admin-stat-card h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Support applications</p>
                                <p class="h3 mb-0">{{ $stats['pending_applications'] }}</p>
                                <small class="text-muted">pending review</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card admin-stat-card h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Donations this month</p>
                                <p class="h3 mb-0">{{ $stats['donations_month'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card admin-stat-card h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Mothers awaiting sponsor</p>
                                <p class="h3 mb-0">{{ $stats['unsponsored_mothers'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card admin-stat-card h-100">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Active programs</p>
                                <p class="h3 mb-0">{{ $stats['active_programs'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if(($formSubmissionStats['total'] ?? 0) > 0)
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                                <h2 class="h6 mb-0">Guest form delivery preference</h2>
                                <a href="{{ route('formSubmissions.admin') }}" class="small">View all</a>
                            </div>
                            @php
                                $wa = $formSubmissionStats['by_channel']['whatsapp'] ?? 0;
                                $tot = max(1, $formSubmissionStats['total']);
                                $waPct = round(100 * $wa / $tot);
                            @endphp
                            <p class="small text-muted mb-3">WhatsApp {{ $wa }} · Email {{ $formSubmissionStats['by_channel']['email'] ?? 0 }} ({{ $formSubmissionStats['total'] }} total)</p>
                            <div class="progress" style="height: 1.25rem;">
                                <div class="progress-bar bg-success" style="width: {{ $waPct }}%"></div>
                                <div class="progress-bar bg-dark" style="width: {{ 100 - $waPct }}%"></div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">Recent support applications</div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0 align-middle">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Phone</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentApplications as $app)
                                                <tr>
                                                    <td>{{ $app->names }}</td>
                                                    <td>{{ $app->phone }}</td>
                                                    <td><span class="badge bg-light text-dark border">{{ $app->status }}</span></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-muted p-4">No applications yet.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="{{ route('AllMembers') }}" class="small">Manage applications →</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header">Upcoming events</div>
                            <ul class="list-group list-group-flush">
                                @forelse($upcomingEvents as $event)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>{{ $event->title }}</span>
                                        <span class="text-muted small">{{ $event->date }}</span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted">No upcoming events.</li>
                                @endforelse
                            </ul>
                            <div class="card-footer bg-white">
                                <a href="{{ route('events') }}" class="small">Manage events →</a>
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
