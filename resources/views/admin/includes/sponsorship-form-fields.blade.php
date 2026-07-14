@php
    // Use formProfile only — never inherit a leftover $profile from a parent @forelse/@foreach.
    $formProfile = $formProfile ?? null;
    $isEdit = $formProfile instanceof \App\Models\Sponsorship;
    $formAction = $isEdit
        ? route('updateSponsorship', $formProfile->id, false)
        : route('saveSponsorship', [], false);
    $types = $types ?? \App\Support\MercyTidesContent::sponsorshipTypes();
@endphp

<form action="{{ $formAction }}" method="POST" enctype="multipart/form-data" data-turbo="false" autocomplete="off">
    @csrf
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Sponsorship type <span class="text-danger">*</span></label>
            <select name="type" class="form-select" required>
                @foreach($types as $key => $meta)
                    <option value="{{ $key }}" {{ old('type', $isEdit ? $formProfile->type : 'child') === $key ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Publish status <span class="text-danger">*</span></label>
            <select name="publish_status" class="form-select" required>
                @foreach(['Published', 'Draft'] as $status)
                    <option value="{{ $status }}" {{ old('publish_status', $isEdit ? ($formProfile->publish_status ?? 'Published') : 'Published') === $status ? 'selected' : '' }}>{{ $status }}</option>
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
            <input type="text" class="form-control" name="names" required value="{{ old('names', $isEdit ? $formProfile->names : '') }}">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Age</label>
            <input type="text" class="form-control" name="age" value="{{ old('age', $isEdit ? ($formProfile->age ?? '') : '') }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Sex</label>
            <select name="sex" class="form-select">
                <option value="">—</option>
                @foreach(['Male', 'Female'] as $sex)
                    <option value="{{ $sex }}" {{ old('sex', $isEdit ? ($formProfile->sex ?? '') : '') === $sex ? 'selected' : '' }}>{{ $sex }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Sponsorship status <span class="text-danger">*</span></label>
            <select name="status" class="form-select" required>
                @foreach(['Not Sponsored', 'Sponsored'] as $status)
                    <option value="{{ $status }}" {{ old('status', $isEdit ? ($formProfile->status ?? 'Not Sponsored') : 'Not Sponsored') === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            <div class="form-check mt-2">
                <input
                    type="checkbox"
                    class="form-check-input"
                    id="show_status_publicly_{{ $isEdit ? $formProfile->id : 'new' }}"
                    name="show_status_publicly"
                    value="1"
                    {{ old('show_status_publicly', $isEdit ? ($formProfile->show_status_publicly ?? false) : false) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="show_status_publicly_{{ $isEdit ? $formProfile->id : 'new' }}">
                    Show this status on the public site
                </label>
            </div>
            <small class="text-muted d-block mt-1">Leave unchecked for mothers already supported internally who are not listed as needing a public sponsor.</small>
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Monthly need (USD)</label>
            <input type="text" class="form-control" name="monthly_need" value="{{ old('monthly_need', $isEdit ? ($formProfile->monthly_need ?? '') : '') }}" placeholder="e.g. 35">
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">URL slug</label>
        <input type="text" class="form-control" name="slug" value="{{ old('slug', $isEdit ? ($formProfile->slug ?? '') : '') }}" placeholder="Auto-generated from name if empty">
    </div>

    <div class="mb-3">
        <label class="form-label">Story / testimonial</label>
        <textarea class="form-control" name="testimany" rows="4" data-editor="rich" placeholder="Their story in their own words">{{ old('testimany', $isEdit ? ($formProfile->testimany ?? '') : '') }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Challenges</label>
        <textarea class="form-control" name="challenges" rows="3" data-editor="rich" placeholder="What they are facing today">{{ old('challenges', $isEdit ? ($formProfile->challenges ?? '') : '') }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Vision / hopes</label>
        <textarea class="form-control" name="vision" rows="3" data-editor="rich" placeholder="Their hopes for the future">{{ old('vision', $isEdit ? ($formProfile->vision ?? '') : '') }}</textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Video URL</label>
        <input type="url" class="form-control" name="video_url" value="{{ old('video_url', $isEdit ? ($formProfile->video_url ?? '') : '') }}" placeholder="YouTube link (optional)">
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Location / address</label>
            <input type="text" class="form-control" name="address" value="{{ old('address', $isEdit ? ($formProfile->address ?? '') : '') }}">
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label">Internal contact phone</label>
            <input type="text" class="form-control" name="phone" value="{{ old('phone', $isEdit ? ($formProfile->phone ?? '') : '') }}">
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 justify-content-end pt-2">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Update profile' : 'Save profile' }}</button>
    </div>
</form>
