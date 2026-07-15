<?php

namespace App\Http\Controllers;

use App\Models\Sponsorship;
use App\Support\MercyTidesContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SponsorshipController extends Controller
{
    public function index(Request $request)
    {
        $typeFilter = $request->input('type');
        $query = Sponsorship::query()->latest();

        if ($typeFilter && array_key_exists($typeFilter, MercyTidesContent::sponsorshipTypes())) {
            $query->ofType($typeFilter);
        }

        return view('admin.sponsorship', [
            'profiles' => $query->get(),
            'types' => MercyTidesContent::sponsorshipTypes(),
            'typeFilter' => $typeFilter,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProfile($request, true);

        $profile = new Sponsorship();
        $this->fillProfile($profile, $validated, $request);

        if ($request->hasFile('image')) {
            $profile->image = $request->file('image')->storeOptimized('images/sponsorship', 'public', ['preset' => 'portrait']);
        }

        $this->syncVideoMedia($profile, $request);

        if (Schema::hasColumn('sponsorships', 'added_by')) {
            $profile->added_by = Auth::id() ?? Auth::guard('admin')->id();
        }

        $profile->save();

        return redirect()->route('sponsorship.index', ['type' => $profile->type])
            ->with('success', 'Sponsorship profile has been added successfully.');
    }

    public function update(Request $request, $id)
    {
        $profile = Sponsorship::query()->find($id);
        if (! $profile) {
            return redirect()
                ->route('sponsorship.index')
                ->with('error', 'That sponsorship profile could not be found. It may have been deleted.');
        }

        $validated = $this->validateProfile($request, false);

        $this->fillProfile($profile, $validated, $request);

        if ($request->hasFile('image')) {
            $this->deleteStoredImage($profile->image);
            $profile->image = $request->file('image')->storeOptimized('images/sponsorship', 'public', ['preset' => 'portrait']);
        }

        $this->syncVideoMedia($profile, $request);

        $profile->save();

        return redirect()->route('sponsorship.index', ['type' => $profile->type])
            ->with('success', 'Sponsorship profile has been updated.');
    }

    public function destroy($id)
    {
        $profile = Sponsorship::findOrFail($id);
        $profile->delete();

        return redirect()->back()->with('success', 'Sponsorship profile has been deleted.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateProfile(Request $request, bool $creating): array
    {
        $types = array_keys(MercyTidesContent::sponsorshipTypes());

        return $request->validate([
            'type' => ['required', 'string', Rule::in($types)],
            'names' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'string', 'max:50'],
            'sex' => ['nullable', 'string', 'max:32'],
            'status' => ['required', 'string', 'max:64'],
            'show_status_publicly' => ['nullable', 'boolean'],
            'publish_status' => ['required', 'string', 'max:64'],
            'phone' => ['nullable', 'string', 'max:64'],
            'contact' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'testimany' => ['nullable', 'string'],
            'challenges' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'video_url' => [
                'nullable',
                'string',
                'max:500',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $url = trim((string) $value);
                    if ($url === '') {
                        return;
                    }
                    if (! preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\//i', $url)) {
                        $fail('Please enter a valid YouTube URL, or leave the field blank.');
                    }
                },
            ],
            'video_file' => [
                'nullable',
                'file',
                'mimetypes:video/mp4,video/webm,video/quicktime',
                'max:61440', // ~60 MB — keep under typical XAMPP upload_max_filesize (64M)
            ],
            'remove_video_file' => ['nullable', 'boolean'],
            'monthly_need' => ['nullable', 'string', 'max:64'],
            'image' => array_filter([
                $creating ? 'required' : 'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:8192',
            ]),
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    protected function fillProfile(Sponsorship $profile, array $validated, Request $request): void
    {
        $profile->type = $validated['type'];
        $profile->names = $validated['names'];
        $slug = trim((string) ($validated['slug'] ?? ''));
        $profile->slug = $slug !== '' ? $slug : null;
        $profile->age = $validated['age'] ?? null;
        $profile->sex = $validated['sex'] ?? null;
        $profile->status = $validated['status'];
        if (Schema::hasColumn('sponsorships', 'show_status_publicly')) {
            $profile->show_status_publicly = $request->boolean('show_status_publicly');
        }
        $profile->publish_status = $validated['publish_status'];
        $profile->phone = $validated['phone'] ?? null;
        $profile->contact = $validated['contact'] ?? null;
        $profile->address = $validated['address'] ?? ($validated['contact'] ?? null);
        $profile->testimany = $validated['testimany'] ?? null;
        $profile->challenges = $validated['challenges'] ?? null;
        $profile->vision = $validated['vision'] ?? null;
        $profile->video_url = filled($validated['video_url'] ?? null) ? trim((string) $validated['video_url']) : null;
        $profile->monthly_need = $validated['monthly_need'] ?? null;
    }

    protected function syncVideoMedia(Sponsorship $profile, Request $request): void
    {
        if (! Schema::hasColumn('sponsorships', 'video_path')) {
            return;
        }

        if ($request->boolean('remove_video_file') && ! $request->hasFile('video_file')) {
            $this->deleteStoredVideo($profile->video_path);
            $profile->video_path = null;
        }

        if ($request->hasFile('video_file')) {
            $this->deleteStoredVideo($profile->video_path);
            $profile->video_path = $request->file('video_file')->store('videos/sponsorship', 'public');
        }
    }

    protected function deleteStoredImage(?string $image): void
    {
        if (empty($image)) {
            return;
        }

        $candidates = array_unique(array_filter([
            ltrim($image, '/'),
            'images/sponsorship/' . ltrim(basename($image), '/'),
            'images/mothers/' . ltrim(basename($image), '/'),
        ]));

        foreach ($candidates as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    protected function deleteStoredVideo(?string $video): void
    {
        if (empty($video)) {
            return;
        }

        $candidates = array_unique(array_filter([
            ltrim($video, '/'),
            'videos/sponsorship/' . ltrim(basename($video), '/'),
        ]));

        foreach ($candidates as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }
}
