<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.product-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['category_id', 'product_category_id']);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $countBefore = ProductCategory::query()->count();
        $slug = $this->uniqueModelSlug(ProductCategory::class, (string) $request->input('name'), null, 'category');

        $category = new ProductCategory();
        $category->name = $request->input('name');
        $category->slug = $slug;
        $category->sort_order = (int) ($request->input('sort_order', 0));
        $category->is_active = $request->has('is_active');

        $this->assertCreatingNew($category);
        $category->save();

        if (ProductCategory::query()->count() !== $countBefore + 1) {
            return redirect()
                ->route('productCategories.index')
                ->with('error', 'Something went wrong while saving. Existing categories were left unchanged.');
        }

        return redirect()->route('productCategories.index')->with('success', 'Category added.');
    }

    public function update(Request $request, $id)
    {
        $category = $this->findAdminRecord(ProductCategory::class, $id);
        $targetId = (int) $category->id;

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $newName = (string) $request->input('name');
        if ($category->name !== $newName) {
            $category->slug = $this->uniqueModelSlug(ProductCategory::class, $newName, $targetId, 'category');
        }
        $category->name = $newName;
        $category->sort_order = (int) ($request->input('sort_order', 0));
        $category->is_active = $request->has('is_active');

        $this->assertSameRecord($category, $targetId);
        $category->save();

        return redirect()->route('productCategories.index')->with('success', 'Category updated.');
    }

    public function destroy($id)
    {
        $category = $this->findAdminRecord(ProductCategory::class, $id);
        if ($category->products()->exists()) {
            return redirect()->route('productCategories.index')->with('error', 'Cannot delete: products still use this category.');
        }
        $category->delete();

        return redirect()->route('productCategories.index')->with('success', 'Category deleted.');
    }
}
