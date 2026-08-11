@extends('layouts.adminbase')

@section('title', 'Leadership Team')

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
                {{-- <h1 class="mt-4">Dashboard</h1> --}}
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Our Team</li>
                </ol>
                <div class="row">
                    @if(session()->has('success'))
                    <div class="arlert alert-success">
                        <button class="close" type="button" data-dismiss="alert">X</button>
                        {{ session()->get('success') }}
                    </div>

                    @endif
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <span>Leadership team</span>
                        <button class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#myModal"><i class="fa fa-plus"></i> Add Team</button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Names</th>
                                    <th>Position</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Biography</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($members as $rs)
                                <tr>
                                    <td>
                                        @if(!empty($rs->image))
                                            <img src="{{ \App\Support\StorageImage::url($rs->image, 'images/staff') }}" alt="{{ $rs->names }}" width="72" class="rounded border">
                                        @else
                                            <span class="text-muted small">No photo</span>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $rs->names }}</td>
                                    <td>{{ $rs->position }}</td>
                                    <td>{{ $rs->phone ?: '—' }}</td>
                                    <td>{{ $rs->facebook ?: ($rs->email ?: '—') }}</td>
                                    <td>
                                        <span class="d-inline-block" style="max-width: 280px;">{{ \Illuminate\Support\Str::limit(strip_tags($rs->bio), 90) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('editStaff', $rs->id) }}" class="btn btn-outline-primary" data-turbo="false">Edit</a>
                                            <x-admin.delete-button :action="route('destroyStaff', $rs->id)" confirm="Delete this staff member?" />
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">No leadership members yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
                        <!-- The Modal for adding new Event -->
                        <div class="modal fade" id="myModal">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">

                                    <!-- Modal Header -->
                                    <div class="modal-header">
                                        <h4 class="modal-title">Adding New Staff Member</h4>
                                        <button type="button" class="btn-close text-black"
                                            data-bs-dismiss="modal">X</button>
                                    </div>

                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <form class="form" action="{{ route('saveStaff') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-body">

                                                <div class="row mb-4">
                                                    <div class="col-lg-8 col-sm-12">
                                                            <label for="names">Staff Names</label>
                                                            <input type="text" class="form-control"
                                                            placeholder="Event Title" name="names"  required="">
                                                    </div>
                                                    <div class="col-lg-4 col-sm-12">
                                                        <label for="position">Position</label>
                                                        <input type="text" class="form-control"
                                                        placeholder="Staff Position" name="position"  required="">
                                                    </div>
                                                    <!-- <div class="col-lg-3 col-sm-12">
                                                        <label for="projectinput1">Category</label>
                                                        <select name="category" id="" class="form-control">
                                                            <option value="Administration" selected="Administration">Administration Team</option>
                                                            <option value="Operations">Operations Team</option>
                                                            <option value="Advisors">Advisors Team</option>
                                                    </select> -->
                                                </div>
                                                </div>

                                                <div class="row mb-4">
                                                    <div class="col-lg-6 col-sm-12">
                                                            <label for="facebook">Phone</label>
                                                            <input type="text" class="form-control"
                                                            placeholder="Facebook" name="phone">
                                                    </div>
                                                    <div class="col-lg-6 col-sm-12">
                                                        <label for="instagram">Email</label>
                                                        <input type="text" class="form-control"
                                                        placeholder="Instagram" name="facebook">
                                                    </div>
                                                    <!-- <div class="col-lg-3 col-sm-12">
                                                        <label for="position">Instagram Page Url</label>
                                                        <input type="text" class="form-control"
                                                        placeholder="Staff Position" name="instagram">
                                                    </div>
                                                    <div class="col-lg-3 col-sm-12">
                                                        <label for="twitter">Twitter Page Url</label>
                                                        <input type="text" class="form-control"
                                                        placeholder="Twitter" name="twitter">
                                                    </div> -->
                                                </div>

                                                <div class="row">

                                                    <div class="col-lg-4 col-sm-12">
                                                            <x-admin.image-field
                                                                label="Staff photo"
                                                                name="image"
                                                                legacy-dir="images/staff"
                                                                preset="portrait"
                                                                :required="true"
                                                                help="Portrait photos work best (about 270×312 pixels)."
                                                            />
                                                    </div>

                                                    <div class="col-lg-8 col-sm-12">
                                                        <label>Biography</label>
                                                        <textarea id="bio" rows="5" class="form-control" name="bio" data-editor="rich" placeholder="Staff BIO"></textarea>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="form-actions mt-5">
                                                <button type="submit" class="btn btn-primary text-black">
                                                    <i class="fa fa-save"></i> Add New Staff
                                                </button>

                                            </div>
                                        </form>
                                    </div>

                                    <!-- Modal footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger text-black"
                                            data-bs-dismiss="modal">Close</button>
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
