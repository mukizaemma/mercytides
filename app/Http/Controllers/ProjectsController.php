<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Program;
use App\Models\Projectimage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectsController extends Controller
{
    public function index()
    {
        $data = Activity::query()->with('program')->latest()->get();
        $programs = Program::query()->orderBy('title')->get();
        return view('admin.activities', ['data' => $data, 'programs' => $programs]);
    }

    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['activity_id', 'project_id']);

        $request->validate(array_merge([
            'title' => ['required', 'string', 'max:255'],
            'program_id' => ['required', 'exists:programs,id'],
            'description' => ['required', 'string'],
        ], $this->imageInputRules('image', required: true)));

        $countBefore = Activity::query()->count();

        $activity = new Activity();
        $activity->title = $request->input('title');
        $activity->description = $request->input('description');
        $activity->program_id = $request->input('program_id');
        $activity->slug = $this->uniqueModelSlug(Activity::class, (string) $request->input('title'), null, 'project');
        if (Schema::hasColumn('activities', 'added_by')) {
            $activity->added_by = Auth::id() ?? Auth::guard('admin')->id();
        }

        $image = $this->imageFromRequest($request, 'image', 'images/projects');
        if ($image) {
            $activity->image = $image;
        }

        $this->assertCreatingNew($activity);
        $activity->save();

        if (Activity::query()->count() !== $countBefore + 1) {
            return redirect()
                ->route('getProjects')
                ->with('error', 'Something went wrong while saving. Existing projects were left unchanged.');
        }

        return redirect()->route('getProjects')->with('success', 'Project created successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data = $this->findAdminRecord(Activity::class, $id);
        $images = $data->images;
        $totalImages = $images->count();
        $programs = Program::query()->orderBy('title')->get();

        return view('admin.activityUpdate', ['data' => $data, 'programs' => $programs, 'images' => $images, 'totalImages' => $totalImages]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(array_merge([
            'title' => ['required', 'string', 'max:255'],
            'program_id' => ['required', 'exists:programs,id'],
            'description' => ['required', 'string'],
        ], $this->imageInputRules('image')));

        $data = $this->findAdminRecord(Activity::class, $id);
        $targetId = (int) $data->id;
        $newTitle = (string) $request->input('title');
        $data->title = $newTitle;
        $data->description = $request->input('description');
        $data->program_id = $request->input('program_id');
        if ($data->slug !== Str::slug($newTitle)) {
            $data->slug = $this->uniqueModelSlug(Activity::class, $newTitle, $targetId, 'project');
        }

        $image = $this->imageFromRequest($request, 'image', 'images/projects');
        if ($image) {
            if (! empty($data->image) && $data->image !== $image && Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }
            $data->image = $image;
        }

        $this->assertSameRecord($data, $targetId);
        $data->save();

        return redirect()->route('editProject', $data->id)->with('success', 'Project has been updated');
    }

    public function destroy($id)
    {
        $data = $this->findAdminRecord(Activity::class, $id);
        $isSuperAdmin = (Auth::user()->email ?? null) === 'admin@iremetech.com';
        $isOwner = !Schema::hasColumn('activities', 'added_by')
            || ((int) ($data->added_by ?? 0) === (int) (Auth::id() ?? Auth::guard('admin')->id()));
        if (! $isSuperAdmin && ! $isOwner) {
            return redirect()->back()->with('error', 'You can only delete initiatives that you created.');
        }
        if (!empty($data->image) && Storage::disk('public')->exists($data->image)) {
            Storage::disk('public')->delete($data->image);
        }
        foreach ($data->images as $img) {
            if (! empty($img->image) && Storage::disk('public')->exists($img->image)) {
                Storage::disk('public')->delete($img->image);
            }
            $img->delete();
        }
        $data->delete();
        return redirect()->route('getProjects')->with('success', 'Project has been deleted');
    }

    public function addProjectImage(Request $request)
    {
        $request->validate([
            'image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'image_paths' => ['nullable', 'array'],
            'image_paths.*' => ['nullable', 'string', 'max:500'],
            'activity_id' => 'required|exists:activities,id',
        ]);

        $paths = $this->galleryFromRequest($request, 'image', 'images/projects/gallery');
        if (empty($paths)) {
            return redirect()->back()->with('error', 'Please select at least one image to upload or choose from the library.');
        }

        $userId = Auth::id() ?? Auth::guard('admin')->id();
        foreach ($paths as $path) {
            Projectimage::create([
                'image' => $path,
                'activity_id' => $request->activity_id,
                'added_by' => $userId,
            ]);
        }

        return redirect()->back()->with('success', 'Project gallery images uploaded successfully!');
    }

    public function deleteProjectImage($id)
    {
        $image = Projectimage::findOrFail($id);

        if (!empty($image->image) && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return redirect()->back()->with('warning', 'Image has been deleted');
    }

}
