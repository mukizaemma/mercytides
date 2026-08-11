@extends('layouts.adminbase')

@section('title', 'About Us')

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
            <div class="container-fluid px-4 py-4">
                <div class="admin-page-header">
                    <h1>About</h1>
                    <p class="text-muted mb-0">Manage mission, values, founder’s story, project background, and impact metrics.</p>
                </div>

                @if(session()->has('success'))
                    <div class="alert alert-success">{{ session()->get('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-4" id="aboutTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="mission-vision-tab" data-bs-toggle="tab" data-bs-target="#mission-vision-pane" type="button" role="tab">Mission &amp; Vision</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="founder-story-tab" data-bs-toggle="tab" data-bs-target="#founder-story-pane" type="button" role="tab">Founder's story</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="core-values-tab" data-bs-toggle="tab" data-bs-target="#core-values-pane" type="button" role="tab">Core values</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="project-background-tab" data-bs-toggle="tab" data-bs-target="#project-background-pane" type="button" role="tab">Project background</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="story-flow-tab" data-bs-toggle="tab" data-bs-target="#story-flow-pane" type="button" role="tab">Problem, solution & manufacturing story</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="impact-tab" data-bs-toggle="tab" data-bs-target="#impact-pane" type="button" role="tab">Impact</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="aboutTabsContent">
                            <div class="tab-pane fade show active" id="mission-vision-pane" role="tabpanel" aria-labelledby="mission-vision-tab">
                                <form action="{{ route('saveAbout', $data->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-lg-6">
                                            <label class="form-label">Mission</label>
                                            <textarea rows="6" class="form-control" name="mission" data-editor="rich">{!! $data->mission !!}</textarea>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">Vision</label>
                                            <textarea rows="6" class="form-control" name="vision" data-editor="rich">{!! $data->vision !!}</textarea>
                                        </div>
                                        <input type="hidden" name="values" value="{{ $data->values }}">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Save mission &amp; vision</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="founder-story-pane" role="tabpanel" aria-labelledby="founder-story-tab">
                                <form action="{{ route('founderStory.update') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <p class="text-muted small mb-0">This content appears on the public <a href="{{ route('foundingStory') }}" target="_blank" rel="noopener">Founder's Story</a> page under About. Upload a portrait of the founder and edit the story at any time.</p>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">Page title</label>
                                            <input type="text" class="form-control" name="title" value="{{ old('title', $founderStory->title) }}" placeholder="Founder's Story">
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">Tagline</label>
                                            <input type="text" class="form-control" name="tagline" value="{{ old('tagline', $founderStory->tagline) }}" placeholder="Breaking Barriers, Bridging A Better Future">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Header caption</label>
                                            <input type="text" class="form-control" name="header_caption" value="{{ old('header_caption', $founderStory->header_caption) }}" placeholder="Short line under the page title">
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">Founder name</label>
                                            <input type="text" class="form-control" name="founder_name" value="{{ old('founder_name', $founderStory->founder_name) }}" placeholder="Mr. MAGAMBO Jonathan">
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">Founder role</label>
                                            <input type="text" class="form-control" name="founder_role" value="{{ old('founder_role', $founderStory->founder_role) }}" placeholder="President &amp; Founder">
                                        </div>
                                        <div class="col-lg-5">
                                            <x-admin.image-field
                                                label="Founder portrait"
                                                name="founder_image"
                                                :current="$founderStory->founder_image ?? null"
                                                legacy-dir="images/founder"
                                                preset="portrait"
                                                help="Portrait of the founder shown beside the story. Upload a new photo or choose one from the media library."
                                            />
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Story</label>
                                            <p class="text-muted small mb-2">Use headings for the context factors (Poverty Legacy, Education, and so on). They will be styled on the public page.</p>
                                            <textarea rows="16" class="form-control" name="content" data-editor="rich">{!! old('content', $founderStory->content) !!}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Save founder's story</button>
                                            <a href="{{ route('foundingStory') }}" class="btn btn-outline-secondary" target="_blank" rel="noopener">Preview page</a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="core-values-pane" role="tabpanel" aria-labelledby="core-values-tab">
                                <form action="{{ route('saveAbout', $data->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Core values — grid list (recommended)</label>
                                            <p class="text-muted small mb-2">Enter <strong>one value per line</strong>. These appear as cards in columns on the About Us page. Leave empty to try auto-detect from the rich text below (bullet list or multiple lines).</p>
                                            <textarea rows="10" class="form-control font-monospace" name="core_values_list" data-editor="plain" placeholder="One value per line">{{ old('core_values_list', $data->core_values_list ?? '') }}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Core values — rich text (optional)</label>
                                            <p class="text-muted small mb-2">Used as fallback if the grid list is empty, or for extra formatting where auto-detect does not apply.</p>
                                            <textarea rows="8" class="form-control" name="values" data-editor="rich">{!! $data->values !!}</textarea>
                                        </div>
                                        <input type="hidden" name="mission" value="{{ $data->mission }}">
                                        <input type="hidden" name="vision" value="{{ $data->vision }}">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Save core values</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="project-background-pane" role="tabpanel" aria-labelledby="project-background-tab">
                                <form action="{{ route('saveBackg', $background->id ?? '') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Project background details</label>
                                            <textarea rows="8" class="form-control" name="description" data-editor="rich">{!! $background->description !!}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Our Approach content</label>
                                            <textarea rows="8" class="form-control" name="approach_content" data-editor="rich">{!! $background->approach_content !!}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Our Model content</label>
                                            <textarea rows="8" class="form-control" name="model_content" data-editor="rich">{!! $background->model_content !!}</textarea>
                                        </div>
                                        <div class="col-lg-6">
                                            <x-admin.image-field
                                                label="Our Model image (diagram/photo)"
                                                name="model_image"
                                                :current="$background->model_image ?? null"
                                                legacy-dir="images"
                                            />
                                        </div>
                                        <div class="col-lg-4">
                                            <x-admin.image-field
                                                label="About cover image"
                                                name="image"
                                                :current="$background->image ?? null"
                                                legacy-dir="images"
                                                preset="hero"
                                            />
                                        </div>
                                        <div class="col-lg-4">
                                            <x-admin.image-field
                                                label="Home background image"
                                                name="image1"
                                                :current="$background->image1 ?? null"
                                                legacy-dir="images"
                                            />
                                        </div>
                                        <div class="col-lg-4">
                                            <x-admin.image-field
                                                label="Pages header image"
                                                name="image2"
                                                :current="$background->image2 ?? null"
                                                legacy-dir="images"
                                            />
                                        </div>
                                        <div class="col-lg-6">
                                            <x-admin.image-field
                                                label="Core values section background (About page)"
                                                name="core_values_background"
                                                :current="$background->core_values_background ?? null"
                                                legacy-dir="images"
                                                preset="hero"
                                                help="Full-width image behind “Our Core Values.” If not set, the pages header image is used."
                                            />
                                        </div>
                                        <div class="col-12">
                                            <input type="hidden" name="donations" value="{{ $background->donations }}">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Save project background</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="story-flow-pane" role="tabpanel" aria-labelledby="story-flow-tab">
                                <form action="{{ route('saveBackg', $background->id ?? '') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Problem statement</label>
                                            <textarea rows="6" class="form-control" name="problem_statement" data-editor="rich">{!! $background->problem_statement !!}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Solution statement</label>
                                            <textarea rows="6" class="form-control" name="solution_statement" data-editor="rich">{!! $background->solution_statement !!}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">What we do</label>
                                            <textarea rows="6" class="form-control" name="what_we_do" data-editor="rich">{!! $background->what_we_do !!}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">How it works (use one step per line or bullet)</label>
                                            <textarea rows="6" class="form-control" name="how_it_works" data-editor="rich">{!! $background->how_it_works !!}</textarea>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">Our expertise</label>
                                            <p class="text-muted small mb-2">Format as: intro line, then items separated by new lines, bullets, or commas. End with a closing line if needed.</p>
                                            <textarea rows="6" class="form-control" name="expertise_content" data-editor="rich">{!! $background->expertise_content !!}</textarea>
                                        </div>
                                        <div class="col-lg-6">
                                            <label class="form-label">Our impact through manufacturing</label>
                                            <p class="text-muted small mb-2">Use new lines or commas between list items. The website will render them as bullets automatically.</p>
                                            <textarea rows="6" class="form-control" name="manufacturing_impact_content" data-editor="rich">{!! $background->manufacturing_impact_content !!}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Products intro</label>
                                            <textarea rows="5" class="form-control" name="products_intro" data-editor="rich">{!! $background->products_intro !!}</textarea>
                                        </div>
                                        <input type="hidden" name="description" value="{{ $background->description }}">
                                        <input type="hidden" name="donations" value="{{ $background->donations }}">
                                        <input type="hidden" name="approach_content" value="{{ $background->approach_content }}">
                                        <input type="hidden" name="model_content" value="{{ $background->model_content }}">
                                        <input type="hidden" name="families_impacted" value="{{ $background->families_impacted }}">
                                        <input type="hidden" name="jobs_created" value="{{ $background->jobs_created }}">
                                        <input type="hidden" name="training_hours" value="{{ $background->training_hours }}">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Save story flow content</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane fade" id="impact-pane" role="tabpanel" aria-labelledby="impact-tab">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="alert alert-light border mb-0">
                                            <p class="mb-2">Impact numbers on the public site are managed as editable items.</p>
                                            <p class="mb-3 text-muted">Add, edit, reorder, or remove stats (for example Mothers empowered, Communities, and any new metrics) from the Impact metrics page.</p>
                                            <a href="{{ route('impacts.index') }}" class="btn btn-primary">
                                                <i class="fa fa-chart-line me-1"></i> Manage impact metrics
                                            </a>
                                        </div>
                                    </div>
                                </div>
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

@push('scripts')
<script>
    (function () {
        var params = new URLSearchParams(window.location.search);
        var tab = params.get('tab');
        if (!tab) return;
        var trigger = document.getElementById(tab + '-tab');
        if (trigger && window.bootstrap && window.bootstrap.Tab) {
            window.bootstrap.Tab.getOrCreateInstance(trigger).show();
        } else if (trigger) {
            trigger.click();
        }
    })();
</script>
@endpush
