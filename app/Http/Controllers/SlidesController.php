<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Slide;

class SlidesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $slides = Slide::latest()->get();
        return view('admin.slides', ['slides'=>$slides]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['slide_id']);

        $request->validate([
            'heading' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
        ]);

        $countBefore = Slide::query()->count();

        $data = new Slide();
        $data->heading = $request->input('heading', 'Default Heading');
        $data->subheading = 'Mercy Tides';

        if ($request->hasFile('image')) {
            $data->image = $request->file('image')->storeOptimized('images/slides', 'public', ['preset' => 'hero']);
        }

        $this->assertCreatingNew($data);
        $stored = $data->save();

        if ($stored && Slide::query()->count() === $countBefore + 1) {
            return redirect('slides')->with('success', 'New Image has been added successfully');
        }

        return redirect()->back()->with('error', 'Failed to add new Image. Existing slides were left unchanged.');
    }

    public function edit($id)
    {
        $data = $this->findAdminRecord(Slide::class, $id);

        return view('admin.slideUpdate', ['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'heading' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
        ]);
        $data = $this->findAdminRecord(Slide::class, $id);
        $targetId = (int) $data->id;
        $data->heading = $request->input('heading');

        if ($request->hasFile('image')) {
            if (! empty($data->image) && Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }
            $data->image = $request->file('image')->storeOptimized('images/slides', 'public', ['preset' => 'hero']);
        }

        $this->assertSameRecord($data, $targetId);
        $data->save();

        return redirect('slides')->with('success', 'Image has been updated');
    }

    public function destroy($id)
    {
        $image = $this->findAdminRecord(Slide::class, $id);
        // delete the image file
        if (!empty($image->image) && Storage::disk('public')->exists($image->image)) {
            Storage::disk('public')->delete($image->image);
        }
        $image->delete();
        return redirect()->back()->with('warning', 'Item has been deleted');
    }
}
