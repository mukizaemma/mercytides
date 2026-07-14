@extends('layouts.frontbase')

@section('title', 'Apply for Support')

@section('content')
@include('frontend.includes.page-header', [
    'title' => 'Apply for Support',
    'caption' => 'Mothers in Uganda can request training, mentorship, and holistic care through Mercy Tides Foundation.',
    'pageKey' => 'apply_for_support',
])

<section class="py-5 grey-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card border-0 shadow-sm site-form-card">
                    <div class="card-body p-4 p-lg-5">
                        <h2 class="h4 mb-2">Support application</h2>
                        <p class="text-muted mb-4">Tell us about yourself and your situation. When you are ready, send your application to our team via WhatsApp or Email.</p>

                        <form class="row g-3 site-partner-form js-public-form" data-form-type="support_application" method="post" action="#" novalidate autocomplete="off">
                            <div class="col-md-6">
                                <label class="form-label">Your full name <span class="text-danger">*</span></label>
                                <input type="text" name="names" class="form-control" required autocomplete="name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone / WhatsApp <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" required autocomplete="tel">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" autocomplete="email">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">District in Uganda</label>
                                <input type="text" name="district" class="form-control" placeholder="e.g. Kampala, Wakiso">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Age</label>
                                <input type="text" name="age" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Female">Female</option>
                                    <option value="Male">Male</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Village / address</label>
                                <input type="text" name="address" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Child information (names, ages)</label>
                                <textarea name="child_info" class="form-control" rows="3" placeholder="Tell us about your children"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Describe your challenge or need <span class="text-danger">*</span></label>
                                <textarea name="challenge" class="form-control" rows="5" required placeholder="How can Mercy Tides support you?"></textarea>
                            </div>
                            @include('frontend.includes.public-form-delivery', ['formType' => 'support'])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
