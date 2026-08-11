<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Program;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        Image::ensureGalleryColumns();
        $synced = Image::syncUploadedImages();

        $visible = Image::query()->onGallery()->ordered()->with('program')->get();
        $hidden = Image::query()->hiddenFromGallery()->ordered()->with('program')->get();
        $programs = Program::latest()->get();

        return view('admin.gallery', [
            'visible' => $visible,
            'hidden' => $hidden,
            'programs' => $programs,
            'synced' => $synced,
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

        $attrs = [
            'caption' => $validated['caption'] ?? null,
            'program_id' => $validated['program_id'] ?? null,
            'youtube_url' => filled($validated['youtube_url'] ?? null) ? trim($validated['youtube_url']) : null,
            'show_on_gallery' => true,
        ];

        if ($image) {
            $row = Image::registerFromPath($image, $attrs);
            if ($row) {
                $row->show_on_gallery = true;
                $row->sort_order = 0;
                $row->save();
            }
        } else {
            $data = new Image();
            $data->caption = $attrs['caption'];
            $data->program_id = $attrs['program_id'];
            $data->youtube_url = $attrs['youtube_url'];
            $data->show_on_gallery = true;
            $data->sort_order = 0;
            $this->assertCreatingNew($data);
            $data->save();
        }

        return redirect()->route('images')->with('success', 'Gallery item has been added successfully.');
    }

    public function edit($id)
    {
        Image::ensureGalleryColumns();
        $data = $this->findAdminRecord(Image::class, $id);
        $programs = Program::latest()->get();

        return view('admin.galleryUpdate', [
            'data' => $data,
            'programs' => $programs,
        ]);
    }

    public function update(Request $request, $id)
    {
        Image::ensureGalleryColumns();
        $data = $this->findAdminRecord(Image::class, $id);
        $targetId = (int) $data->id;

        $validated = $request->validate(array_merge([
            'caption' => ['nullable', 'string', 'max:255'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'youtube_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|shorts\/)|youtu\.be\/).+/i'],
            'show_on_gallery' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
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
        $data->show_on_gallery = $request->boolean('show_on_gallery');
        if (array_key_exists('sort_order', $validated) && $validated['sort_order'] !== null) {
            $data->sort_order = (int) $validated['sort_order'];
        }

        if ($image) {
            $data->image = $image;
        }

        $this->assertSameRecord($data, $targetId);
        $data->save();

        return redirect()->route('images')->with('success', 'Gallery item has been updated.');
    }

    public function toggle(Request $request, $id)
    {
        Image::ensureGalleryColumns();
        $image = $this->findAdminRecord(Image::class, $id);
        $show = $request->has('show')
            ? $request->boolean('show')
            : ! $image->show_on_gallery;

        $image->show_on_gallery = $show;
        if ($show) {
            $image->sort_order = 0;
        }
        $image->save();

        $message = $show
            ? 'Image is now shown on the Gallery page.'
            : 'Image was removed from the Gallery page. The file is still in the library.';

        return redirect()->route('images')->with('success', $message);
    }

    public function reorder(Request $request)
    {
        Image::ensureGalleryColumns();
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:images,id'],
        ]);

        foreach (array_values($validated['ids']) as $index => $id) {
            Image::query()->whereKey($id)->update(['sort_order' => ($index + 1) * 10]);
        }

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('images')->with('success', 'Gallery order saved.');
    }

    public function destroy($id)
    {
        $image = $this->findAdminRecord(Image::class, $id);
        $image->show_on_gallery = false;
        $image->save();
        $image->delete();

        return redirect()->back()->with('warning', 'Gallery catalog entry removed. The original file was kept so other pages are not broken.');
    }
}
