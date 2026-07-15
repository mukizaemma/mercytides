<?php

namespace App\Http\Controllers;

use App\Models\Impact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ImpactsController extends Controller
{
    public function index()
    {
        $impacts = Impact::latest()->get();

        return view('admin.impacts', ['impacts' => $impacts]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['impact_id']);

        $request->validate([
            'title' => 'required|max:255',
            'value' => 'nullable|string|max:255',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $countBefore = Impact::query()->count();

        $fileName = '';
        if ($request->hasFile('image')) {
            $fileName = $this->storeOptimizedImageBasename(
                $request->file('image'),
                'images/impacts',
                'public'
            );
        }

        $title = (string) $request->input('title');
        $slug = $this->uniqueModelSlug(Impact::class, $title, null, 'impact');

        $impact = new Impact();
        $impact->title = $title;
        $impact->value = $request->input('value');
        $impact->description = $request->input('description');
        $impact->image = $fileName;
        $impact->slug = $slug;

        $this->assertCreatingNew($impact);
        $impact->save();

        if (Impact::query()->count() !== $countBefore + 1) {
            return redirect()
                ->route('impacts.index')
                ->with('error', 'Something went wrong while saving. Existing impacts were left unchanged.');
        }

        return redirect()->route('impacts.index')->with('success', 'Impact created successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data = $this->findAdminRecord(Impact::class, $id);

        return view('admin.impactUpdate', ['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $impact = $this->findAdminRecord(Impact::class, $id);
        $targetId = (int) $impact->id;
        $imagePath = 'public/images/impacts/'.$impact->image;

        $request->validate([
            'title' => 'required|max:255',
            'value' => 'nullable|string|max:255',
            'description' => 'required',
            'image' => 'image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }

            $fileName = $this->storeOptimizedImageBasename(
                $request->file('image'),
                'images/impacts',
                'public'
            );
        } else {
            $fileName = $impact->image;
        }

        $newTitle = (string) $request->input('title');
        if ($impact->title !== $newTitle) {
            $impact->slug = $this->uniqueModelSlug(Impact::class, $newTitle, $targetId, 'impact');
        }

        $impact->title = $newTitle;
        $impact->value = $request->input('value');
        $impact->description = $request->input('description');
        $impact->image = $fileName;

        $this->assertSameRecord($impact, $targetId);
        $impact->save();

        return redirect()->route('impacts.index')->with('success', 'Impact updated successfully');
    }

    public function destroy($id)
    {
        $impact = $this->findAdminRecord(Impact::class, $id);
        $imagePath = storage_path('app/public/images/impacts/').$impact->image;

        if (File::exists($imagePath)) {
            File::delete($imagePath);
        }

        $impact->delete();

        return redirect()->back()->with('success', 'Impact and its image was deleted');
    }
}
