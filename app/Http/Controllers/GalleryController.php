<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        $validated = $request->validate([
            'caption' => ['nullable', 'string', 'max:255'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'youtube_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|shorts\/)|youtu\.be\/).+/i'],
            'image' => [
                Rule::requiredIf(fn () => blank($request->input('youtube_url'))),
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif,webp',
                'max:4096',
            ],
        ], [
            'image.required' => 'Upload an image or provide a YouTube URL.',
            'youtube_url.regex' => 'Enter a valid YouTube URL.',
        ]);

        if (blank($validated['youtube_url'] ?? null) && ! $request->hasFile('image')) {
            return redirect()->back()->with('error', 'Upload an image or provide a YouTube URL.')->withInput();
        }

        $data = new Image();
        $data->caption = $validated['caption'] ?? null;
        $data->program_id = $validated['program_id'] ?? null;
        $data->youtube_url = filled($validated['youtube_url'] ?? null) ? trim($validated['youtube_url']) : null;

        if ($request->hasFile('image')) {
            $data->image = $request->file('image')->storeOptimized('images/gallery', 'public');
        }

        $data->save();

        return redirect()->route('images')->with('success', 'Gallery item has been added successfully.');
    }

    public function edit($id)
    {
        $data = Image::query()->findOrFail($id);
        $programs = Program::latest()->get();

        return view('admin.galleryUpdate', [
            'data' => $data,
            'programs' => $programs,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = Image::query()->findOrFail($id);

        $validated = $request->validate([
            'caption' => ['nullable', 'string', 'max:255'],
            'program_id' => ['nullable', 'exists:programs,id'],
            'youtube_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|shorts\/)|youtu\.be\/).+/i'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
        ], [
            'youtube_url.regex' => 'Enter a valid YouTube URL.',
        ]);

        $youtube = filled($validated['youtube_url'] ?? null) ? trim($validated['youtube_url']) : null;

        if (! $request->hasFile('image') && blank($youtube) && empty($data->image)) {
            return redirect()->back()->with('error', 'Keep an image, upload a new one, or provide a YouTube URL.')->withInput();
        }

        $data->caption = $validated['caption'] ?? null;
        $data->program_id = $validated['program_id'] ?? null;
        $data->youtube_url = $youtube;

        if ($request->hasFile('image')) {
            if (! empty($data->image) && Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }
            $data->image = $request->file('image')->storeOptimized('images/gallery', 'public');
        }

        $data->save();

        return redirect()->route('images')->with('success', 'Gallery item has been updated.');
    }

    public function destroy($id)
    {
        $image = Image::query()->findOrFail($id);

        if (! empty($image->image) && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }

        $image->delete();

        return redirect()->back()->with('warning', 'Gallery item has been deleted.');
    }
}
