@extends('layouts.adminbase')

@section('title', 'Background')

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
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header">
                                    <h2  class="btn btn-primary">Our Background</h2>
                                    @if(session()->has('success'))
                                    <div class="arlert alert-success">
                                        <button class="close" type="button" data-dismiss="alert">X</button>
                                        {{ session()->get('success') }}
                                    </div>

                                    @endif
                                </div>
                                <!-- ./card-header -->
                                <div class="card-body">
                                    <form class="form" action="{{ route('saveBackg',$data->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="projectinput1">About Us Details</label>
                                                    <textarea id="background" rows="10" class="form-control" name="description" data-editor="rich">{!!$data->description!!}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="projectinput1">About Donations at Mercy Tides</label>
                                                    <textarea id="background" rows="10" class="form-control" name="donations" data-editor="rich">{!!$data->donations!!}</textarea>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="get_involved_intro">Get Involved page — why participate</label>
                                                    <textarea id="get_involved_intro" rows="8" class="form-control" name="get_involved_intro" data-editor="rich">{!! $data->get_involved_intro ?? '' !!}</textarea>
                                                    <small class="text-muted">Shown in full on the public Get Involved page.</small>
                                                </div>
                                            </div>

                                        </div>

                                            <div class="row g-3">
                                                <div class="col-lg-4 col-sm-12">
                                                    <x-admin.image-field
                                                        label="About cover image"
                                                        name="image"
                                                        :current="$data->image ?? null"
                                                        legacy-dir="images"
                                                        preset="hero"
                                                    />
                                                </div>

                                                <div class="col-lg-4 col-sm-12">
                                                    <x-admin.image-field
                                                        label="Home background image"
                                                        name="image1"
                                                        :current="$data->image1 ?? null"
                                                        legacy-dir="images"
                                                    />
                                                </div>

                                                <div class="col-lg-4 col-sm-12">
                                                    <x-admin.image-field
                                                        label="Pages header image"
                                                        name="image2"
                                                        :current="$data->image2 ?? null"
                                                        legacy-dir="images"
                                                    />
                                                </div>
                                            </div>
                                        </div>


                                    <div class="form-actions mt-5">
                                        <button type="submit" class="btn btn-primary text-black">
                                            <i class="fa fa-save"></i> Save Changes
                                        </button>

                                    </div>
                                      </form>

                                 </div>
                              </div>
                                </div>
                            </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->


                        </div>
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
        </main>
        @include('admin.includes.footer')
    </div>
</div>

@section('scripts')

<script src="{{asset('assets')}}/js/summernote.js"></script>

@endsection
