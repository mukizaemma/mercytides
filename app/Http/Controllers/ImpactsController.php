<?php

namespace App\Http\Controllers;

use App\Models\Impact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class ImpactsController extends Controller
{
    public function index()
    {
        $impacts = Impact::query()->ordered()->get();

        return view('admin.impacts', ['impacts' => $impacts]);
    }

    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['impact_id']);

        $request->validate([
            'title' => 'required|max:255',
            'value' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:Active,Inactive',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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
        $impact->value = trim((string) $request->input('value'));
        $impact->description = $request->input('description') ?: null;
        $impact->image = $fileName !== '' ? $fileName : null;
        $impact->slug = $slug;
        $impact->status = $request->input('status', 'Active');
        $impact->sort_order = $this->resolveSortOrder($request->input('sort_order'), $countBefore);

        $this->assertCreatingNew($impact);
        $impact->save();

        if (Impact::query()->count() !== $countBefore + 1) {
            return redirect()
                ->route('impacts.index')
                ->with('error', 'Something went wrong while saving. Existing impacts were left unchanged.');
        }

        return redirect()->route('impacts.index')->with('success', 'Impact metric created successfully');
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

        $request->validate([
            'title' => 'required|max:255',
            'value' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:Active,Inactive',
            'sort_order' => 'nullable|integer|min:0|max:9999',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $fileName = $impact->image;
        if ($request->hasFile('image')) {
            $this->deleteImpactImage($impact->image);

            $fileName = $this->storeOptimizedImageBasename(
                $request->file('image'),
                'images/impacts',
                'public'
            );
        }

        $newTitle = (string) $request->input('title');
        if ($impact->title !== $newTitle) {
            $impact->slug = $this->uniqueModelSlug(Impact::class, $newTitle, $targetId, 'impact');
        }

        $impact->title = $newTitle;
        $impact->value = trim((string) $request->input('value'));
        $impact->description = $request->input('description') ?: null;
        $impact->image = $fileName ?: null;
        $impact->status = $request->input('status', $impact->status ?: 'Active');
        if (Schema::hasColumn('impacts', 'sort_order')) {
            $impact->sort_order = (int) ($request->input('sort_order') ?? $impact->sort_order ?? 0);
        }

        $this->assertSameRecord($impact, $targetId);
        $impact->save();

        return redirect()->route('impacts.index')->with('success', 'Impact metric updated successfully');
    }

    public function destroy($id)
    {
        $impact = $this->findAdminRecord(Impact::class, $id);
        $this->deleteImpactImage($impact->image);
        $impact->delete();

        return redirect()->back()->with('success', 'Impact metric deleted');
    }

    private function resolveSortOrder(mixed $requested, int $fallback): int
    {
        if ($requested === null || $requested === '') {
            return $fallback;
        }

        return max(0, (int) $requested);
    }

    private function deleteImpactImage(?string $image): void
    {
        if (! $image) {
            return;
        }

        $path = storage_path('app/public/images/impacts/'.$image);
        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
