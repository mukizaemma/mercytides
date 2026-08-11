<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $images = Image::query()->with('program')->latest()->get();
        $programs = Program::latest()->get();

        return view('admin.gallery', [
            'images' => $images,
            'programs' => $programs,
        ]);
    }

    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['gallery_id', 'image_id']);

        $validated = $request->validate(array_merge([
            'caption' => ['nullable', 'string', 'max:255'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'youtube_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|shorts\/)|youtu\.be\/).+/i'],
        ], $this->imageInputRules('image')), [
            'youtube_url.regex' => 'Enter a valid YouTube URL.',
        ]);

        $image = $this->imageFromRequest($request, 'image', 'images/gallery');
        if (blank($validated['youtube_url'] ?? null) && ! $image) {
            return redirect()->back()->with('error', 'Upload an image, choose one from the library, or provide a YouTube URL.')->withInput();
        }

        $countBefore = Image::query()->count();

        $data = new Image();
        $data->caption = $validated['caption'] ?? null;
        $data->program_id = $validated['program_id'] ?? null;
        $data->youtube_url = filled($validated['youtube_url'] ?? null) ? trim($validated['youtube_url']) : null;

        if ($image) {
            $data->image = $image;
        }

        $this->assertCreatingNew($data);
        $data->save();

        if (Image::query()->count() !== $countBefore + 1) {
            return redirect()
                ->route('images')
                ->with('error', 'Something went wrong while saving. Existing gallery items were left unchanged.');
        }

        return redirect()->route('images')->with('success', 'Gallery item has been added successfully.');
    }

    public function edit($id)
    {
        $data = $this->findAdminRecord(Image::class, $id);
        $programs = Program::latest()->get();

        return view('admin.galleryUpdate', [
            'data' => $data,
            'programs' => $programs,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = $this->findAdminRecord(Image::class, $id);
        $targetId = (int) $data->id;

        $validated = $request->validate(array_merge([
            'caption' => ['nullable', 'string', 'max:255'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'youtube_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|shorts\/)|youtu\.be\/).+/i'],
        ], $this->imageInputRules('image')), [
            'youtube_url.regex' => 'Enter a valid YouTube URL.',
        ]);

        $youtube = filled($validated['youtube_url'] ?? null) ? trim($validated['youtube_url']) : null;
        $image = $this->imageFromRequest($request, 'image', 'images/gallery');

        if (! $image && blank($youtube) && empty($data->image)) {
            return redirect()->back()->with('error', 'Keep an image, upload or choose a new one, or provide a YouTube URL.')->withInput();
        }

        $data->caption = $validated['caption'] ?? null;
        $data->program_id = $validated['program_id'] ?? null;
        $data->youtube_url = $youtube;

        if ($image) {
            if (! empty($data->image) && $data->image !== $image && Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }
            $data->image = $image;
        }

        $this->assertSameRecord($data, $targetId);
        $data->save();

        return redirect()->route('images')->with('success', 'Gallery item has been updated.');
    }

    public function destroy($id)
    {
        $image = $this->findAdminRecord(Image::class, $id);

        if (! empty($image->image) && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return redirect()->back()->with('warning', 'Gallery item has been deleted.');
    }
}
