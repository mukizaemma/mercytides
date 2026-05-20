@extends('layouts.frontbase')

@section('title', 'Volunteer')

@section('content')
@include('frontend.includes.page-header', [
    'title' => 'Volunteer',
    'caption' => 'Share your time, skills, and heart with mothers and families served by Mercy Tides Foundation in Uganda.',
])

<section class="py-5 grey-bg">
    <div class="container">
        <div class="row justify-content-center mb-4 mb-lg-5">
            <div class="col-lg-10">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 get-involved-highlight">
                            <div class="card-body p-4">
                                <h3 class="h5 mb-3">Mentoring &amp; training</h3>
                                <p class="text-muted mb-0 small">Coach mothers in vocational skills, business basics, or life skills during workshops and site visits.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 get-involved-highlight">
                            <div class="card-body p-4">
                                <h3 class="h5 mb-3">Programs &amp; events</h3>
                                <p class="text-muted mb-0 small">Help with discipleship gatherings, conferences, outreach, and community activities.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100 get-involved-highlight">
                            <div class="card-body p-4">
                                <h3 class="h5 mb-3">Remote support</h3>
                                <p class="text-muted mb-0 small">Communications, design, fundraising, prayer partnerships, and professional advice from anywhere.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
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

                <div class="card border-0 shadow-sm site-form-card">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="h4 mb-2">Volunteer application</h2>
                        <p class="text-muted mb-4">Tell us about yourself and how you would like to serve. Our team will follow up by email or phone.</p>

                        <form class="row g-3 site-partner-form js-public-form" data-form-type="volunteer" method="post" action="#" novalidate autocomplete="off" data-turbo="false">

                            <div class="col-md-6">
                                <label class="form-label">Full name <span class="text-danger">*</span></label>
                                <input type="text" name="names" class="form-control" required value="{{ old('names') }}" autocomplete="name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required value="{{ old('email') }}" autocomplete="email">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone / WhatsApp</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" autocomplete="tel">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Country <span class="text-danger">*</span></label>
                                <input type="text" name="country" class="form-control" required value="{{ old('country', 'Uganda') }}" autocomplete="country-name">
                            </div>
                            <div class="col-12">
                                <label class="form-label">City / address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="Where you are based">
                            </div>
                            @if($programs->isNotEmpty())
                                <div class="col-12">
                                    <label class="form-label">Program you would like to support (optional)</label>
                                    <select name="program_id" class="form-control">
                                        <option value="">Any program / general volunteering</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}" @selected((string) old('program_id') === (string) $program->id)>{{ $program->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-12">
                                <label class="form-label">About you</label>
                                <textarea name="aboutYou" class="form-control" rows="3" placeholder="Brief background, faith community, availability…">{{ old('aboutYou') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Skills &amp; experience</label>
                                <textarea name="career" class="form-control" rows="3" placeholder="Professional skills, languages, certifications…">{{ old('career') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">How would you like to serve? <span class="text-danger">*</span></label>
                                <textarea name="howToServe" class="form-control" rows="4" required placeholder="Mentoring, teaching, logistics, prayer, remote support, duration of commitment…">{{ old('howToServe') }}</textarea>
                            </div>
                            @include('frontend.includes.public-form-delivery', ['formType' => 'volunteer'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
