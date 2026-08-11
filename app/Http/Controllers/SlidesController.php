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

        $request->validate(array_merge([
            'heading' => ['nullable', 'string', 'max:255'],
        ], $this->imageInputRules('image', required: true)));

        $countBefore = Slide::query()->count();

        $data = new Slide();
        $data->heading = $request->input('heading', 'Default Heading');
        $data->subheading = 'Mercy Tides';

        $image = $this->imageFromRequest($request, 'image', 'images/slides', ['preset' => 'hero']);
        if ($image) {
            $data->image = $image;
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
        $request->validate(array_merge([
            'heading' => ['nullable', 'string', 'max:255'],
        ], $this->imageInputRules('image')));
        $data = $this->findAdminRecord(Slide::class, $id);
        $targetId = (int) $data->id;
        $data->heading = $request->input('heading');

        $image = $this->imageFromRequest($request, 'image', 'images/slides', ['preset' => 'hero']);
        if ($image) {
            if (! empty($data->image) && $data->image !== $image && Storage::disk('public')->exists($data->image)) {
                Storage::disk('public')->delete($data->image);
            }
            $data->image = $image;
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
