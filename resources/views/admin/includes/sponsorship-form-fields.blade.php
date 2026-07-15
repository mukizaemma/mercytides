@php
    // Use formProfile only — never inherit a leftover $profile from a parent @forelse/@foreach.
    $forceCreate = (bool) ($forceCreate ?? false);
    $formProfile = $forceCreate ? null : ($formProfile ?? null);
    $isEdit = ! $forceCreate && $formProfile instanceof \App\Models\Sponsorship;
    $profileId = $isEdit ? (int) $formProfile->getKey() : null;
    $formIntent = $isEdit ? 'edit-'.$profileId : 'create';
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $activeIntent = old('form_intent');
    $useOld = $errors->any() && $activeIntent === $formIntent;
    $field = static function (string $key, $fallback = '') use ($useOld) {
        return $useOld ? old($key, $fallback) : $fallback;
    };
    // Relative URLs so edits stay on the current host (APP_URL may differ from the public domain).
    $formAction = $isEdit
        ? route('updateSponsorship', ['id' => $profileId], false)
        : route('saveSponsorship', [], false);
    $types = $types ?? \App\Support\MercyTidesContent::sponsorshipTypes();
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" data-turbo="false" autocomplete="off">
    @csrf
    <input type="hidden" name="form_intent" value="{{ $formIntent }}">
    @if($isEdit)
        <input type="hidden" name="sponsorship_id" value="{{ $profileId }}">
        @method('PUT')
    @endif
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Sponsorship type <span class="text-danger">*</span></label>
            <select name="type" class="form-select" required>
                @foreach($types as $key => $meta)
                    <option value="{{ $key }}" {{ $field('type', $isEdit ? $formProfile->type : 'child') === $key ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Publish status <span class="text-danger">*</span></label>
            <select name="publish_status" class="form-select" required>
                @foreach(['Published', 'Draft'] as $status)
                    <option value="{{ $status }}" {{ $field('publish_status', $isEdit ? ($formProfile->publish_status ?? 'Published') : 'Published') === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Portrait photo @if(!$isEdit)<span class="text-danger">*</span>@endif</label>
        <input type="file" class="form-control" name="image" {{ $isEdit ? '' : 'required' }} accept="image/*" data-image-preset="portrait">
        @if($isEdit && !empty($formProfile->image))
            <img src="{{ \App\Models\Sponsorship::publicImageUrl($formProfile->image) }}" alt="" class="mt-2 rounded border" width="90" height="120" style="object-fit:cover;">
        @endif
    </div>

    <div class="row">
        <div class="col-md-8 mb-3">
            <label class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="names" required value="{{ $field('names', $isEdit ? $formProfile->names : '') }}">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Age</label>
            <input type="text" class="form-control" name="age" value="{{ $field('age', $isEdit ? ($formProfile->age ?? '') : '') }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Sex</label>
            <select name="sex" class="form-select">
                <option value="">—</option>
                @foreach(['Male', 'Female'] as $sex)
                    <option value="{{ $sex }}" {{ $field('sex', $isEdit ? ($formProfile->sex ?? '') : '') === $sex ? 'selected' : '' }}>{{ $sex }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Sponsorship status <span class="text-danger">*</span></label>
            <select name="status" class="form-select" required>
                @foreach(['Not Sponsored', 'Sponsored'] as $status)
                    <option value="{{ $status }}" {{ $field('status', $isEdit ? ($formProfile->status ?? 'Not Sponsored') : 'Not Sponsored') === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            <div class="form-check mt-2">
                <input
                    type="checkbox"
                    class="form-check-input"
                    id="show_status_publicly_{{ $isEdit ? $profileId : 'new' }}"
                    name="show_status_publicly"
                    value="1"
                    {{ ($useOld ? old('show_status_publicly') : ($isEdit ? ($formProfile->show_status_publicly ?? false) : false)) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="show_status_publicly_{{ $isEdit ? $profileId : 'new' }}">
                    Show this status on the public site
                </label>
            </div>
            <small class="text-muted d-block mt-1">Leave unchecked for mothers already supported internally who are not listed as needing a public sponsor.</small>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Monthly need (USD)</label>
            <input type="text" class="form-control" name="monthly_need" value="{{ $field('monthly_need', $isEdit ? ($formProfile->monthly_need ?? '') : '') }}" placeholder="e.g. 35">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">URL slug</label>
        <input type="text" class="form-control" name="slug" value="{{ $field('slug', $isEdit ? ($formProfile->slug ?? '') : '') }}" placeholder="Auto from name — updates when the name changes">
        <small class="text-muted">Leave blank to generate from the name. Editing the name also refreshes this slug.</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Story / testimonial</label>
        <textarea class="form-control" name="testimany" rows="4" data-editor="rich" placeholder="Their story in their own words">{!! $field('testimany', $isEdit ? ($formProfile->testimany ?? '') : '') !!}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Challenges</label>
        <textarea class="form-control" name="challenges" rows="3" data-editor="rich" placeholder="What they are facing today">{!! $field('challenges', $isEdit ? ($formProfile->challenges ?? '') : '') !!}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Vision / hopes</label>
        <textarea class="form-control" name="vision" rows="3" data-editor="rich" placeholder="Their hopes for the future">{!! $field('vision', $isEdit ? ($formProfile->vision ?? '') : '') !!}</textarea>
    </div>

    <div class="mb-3 p-3 border rounded bg-light">
        <label class="form-label fw-semibold mb-2">Story video</label>
        <p class="text-muted small mb-3">
            Add a YouTube link, upload a file, or both. If a file is uploaded, it plays on the public page;
            otherwise the YouTube video is used. Prefer <strong>landscape MP4 (H.264, 1080p)</strong> for uploads — max about 60&nbsp;MB on this server.
            For larger films, upload to YouTube (unlisted) and paste the link.
        </p>

        <div class="mb-3">
            <label class="form-label">YouTube URL</label>
            <input
                type="url"
                class="form-control"
                name="video_url"
                value="{{ $field('video_url', $isEdit ? ($formProfile->video_url ?? '') : '') }}"
                placeholder="https://www.youtube.com/watch?v=… or https://youtu.be/…"
            >
            @if($useOld && $errors->has('video_url'))
                <div class="text-danger small mt-1">{{ $errors->first('video_url') }}</div>
            @endif
        </div>

        <div class="mb-0">
            <label class="form-label">Upload video file</label>
            <input
                type="file"
                class="form-control"
                name="video_file"
                accept="video/mp4,video/webm,video/quicktime,.mp4,.webm,.mov"
            >
            @if($useOld && $errors->has('video_file'))
                <div class="text-danger small mt-1">{{ $errors->first('video_file') }}</div>
            @endif
            @if($isEdit && !empty($formProfile->video_path))
                <div class="mt-2">
                    <video
                        class="rounded border w-100"
                        style="max-height: 220px; background:#0a1628;"
                        controls
                        preload="metadata"
                        src="{{ \App\Models\Sponsorship::publicVideoUrl($formProfile->video_path) }}"
                    ></video>
                    <div class="form-check mt-2">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="remove_video_file_{{ $profileId }}"
                            name="remove_video_file"
                            value="1"
                            {{ ($useOld && old('remove_video_file')) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="remove_video_file_{{ $profileId }}">
                            Remove uploaded video
                        </label>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Location / address</label>
            <input type="text" class="form-control" name="address" value="{{ $field('address', $isEdit ? ($formProfile->address ?? '') : '') }}">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Internal contact phone</label>
            <input type="text" class="form-control" name="phone" value="{{ $field('phone', $isEdit ? ($formProfile->phone ?? '') : '') }}">
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 justify-content-end pt-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update profile' : 'Save profile' }}</button>
    </div>
</form>
