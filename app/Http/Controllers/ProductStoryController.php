<?php

namespace App\Http\Controllers;

use App\Models\ProductStoryPoint;
use App\Models\ProductStorySetting;
use Illuminate\Http\Request;

class ProductStoryController extends Controller
{
    /**
     * Accept comma/newline/bullet separated entries from admin and normalize.
     */
    private function parsePointItems(string $raw): array
    {
        $parts = preg_split('/[\r\n,•]+/u', $raw) ?: [];
        $items = [];

        foreach ($parts as $part) {
            $text = trim((string) $part);
            if ($text !== '') {
                $items[] = $text;
            }
        }

        return $items;
    }

    public function index()
    {
        $setting = ProductStorySetting::firstOrSingleton();
        $points = ProductStoryPoint::query()->ordered()->get();

        return view('admin.product-story.index', compact('setting', 'points'));
    }

    public function updateHeading(Request $request)
    {
        $data = $request->validate([
            'heading' => ['nullable', 'string', 'max:255'],
        ]);

        $setting = ProductStorySetting::firstOrSingleton();
        $setting->heading = $data['heading'] ?? '';
        $setting->save();

        return redirect()->route('productStory.index')->with('success', 'Section heading saved.');
    }

    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['point_id', 'product_story_point_id']);

        $data = $request->validate([
            'content' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $countBefore = ProductStoryPoint::query()->count();
        $baseSort = (int) ($data['sort_order'] ?? 0);
        $items = $this->parsePointItems($data['content']);

        if ($items === []) {
            $items = [trim($data['content'])];
        }

        foreach ($items as $index => $item) {
            $point = new ProductStoryPoint();
            $point->content = $item;
            $point->sort_order = $baseSort + $index;
            $point->is_active = true;
            $this->assertCreatingNew($point);
            $point->save();
        }

        if (ProductStoryPoint::query()->count() !== $countBefore + count($items)) {
            return redirect()
                ->route('productStory.index')
                ->with('error', 'Something went wrong while saving. Existing story points were left unchanged.');
        }

        $msg = count($items) > 1 ? 'Points added from comma/newline list.' : 'Point added.';

        return redirect()->route('productStory.index')->with('success', $msg);
    }

    public function update(Request $request, $id)
    {
        $point = $this->findAdminRecord(ProductStoryPoint::class, $id);
        $targetId = (int) $point->id;

        $data = $request->validate([
            'content' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $point->content = $data['content'];
        $point->sort_order = (int) ($data['sort_order'] ?? 0);
        $point->is_active = $request->has('is_active');

        $this->assertSameRecord($point, $targetId);
        $point->save();

        return redirect()->route('productStory.index')->with('success', 'Point updated.');
    }

    public function destroy($id)
    {
        $this->findAdminRecord(ProductStoryPoint::class, $id)->delete();

        return redirect()->route('productStory.index')->with('success', 'Point removed.');
    }
}
