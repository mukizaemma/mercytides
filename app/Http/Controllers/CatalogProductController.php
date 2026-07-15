<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CatalogProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->with('category')->latest()->paginate(25);

        return view('admin.catalog-products.index', compact('products'));
    }

    public function create()
    {
        $categories = ProductCategory::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.catalog-products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['product_id']);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'is_new' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $countBefore = Product::query()->count();
        $slug = $this->uniqueModelSlug(Product::class, (string) $request->input('title'), null, 'product');

        $product = new Product();
        $product->title = $request->input('title');
        $product->slug = $slug;
        $product->product_category_id = (int) $request->input('product_category_id');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->compare_at_price = $request->input('compare_at_price');
        $product->stock_quantity = (int) ($request->input('stock_quantity', 0));
        $product->color = $request->input('color');
        $product->sort_order = (int) ($request->input('sort_order', 0));
        $product->is_new = $request->has('is_new');
        $product->is_active = $request->has('is_active');

        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->storeOptimized('images/products', 'public');
        }

        $this->assertCreatingNew($product);
        $product->save();

        if (Product::query()->count() !== $countBefore + 1) {
            return redirect()
                ->route('catalogProducts.index')
                ->with('error', 'Something went wrong while saving. Existing products were left unchanged.');
        }

        $this->storeGalleryImages($product, $request);

        return redirect()->route('catalogProducts.edit', $product->id)->with('success', 'Product created.');
    }

    public function edit($id)
    {
        $product = Product::with('images')->findOrFail((int) $id);
        $categories = ProductCategory::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.catalog-products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = $this->findAdminRecord(Product::class, $id);
        $targetId = (int) $product->id;

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'is_new' => ['boolean'],
            'is_active' => ['boolean'],
        ]);

        $newTitle = (string) $request->input('title');
        if ($product->title !== $newTitle) {
            $product->slug = $this->uniqueModelSlug(Product::class, $newTitle, $targetId, 'product');
        }
        $product->title = $newTitle;
        $product->product_category_id = (int) $request->input('product_category_id');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->compare_at_price = $request->input('compare_at_price');
        $product->stock_quantity = (int) ($request->input('stock_quantity', 0));
        $product->color = $request->input('color');
        $product->sort_order = (int) ($request->input('sort_order', 0));
        $product->is_new = $request->has('is_new');
        $product->is_active = $request->has('is_active');

        if ($request->hasFile('image')) {
            if (! empty($product->image) && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->storeOptimized('images/products', 'public');
        }

        $this->assertSameRecord($product, $targetId);
        $product->save();

        $this->storeGalleryImages($product, $request);

        return redirect()->route('catalogProducts.edit', $product->id)->with('success', 'Product updated.');
    }

    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail((int) $id);

        if (! empty($product->image) && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }
        foreach ($product->images as $img) {
            if (! empty($img->image) && Storage::disk('public')->exists($img->image)) {
                Storage::disk('public')->delete($img->image);
            }
            $img->delete();
        }

        $product->delete();

        return redirect()->route('catalogProducts.index')->with('success', 'Product deleted.');
    }

    public function deleteImage($id)
    {
        $img = ProductImage::findOrFail($id);
        $productId = $img->product_id;

        if (! empty($img->image) && Storage::disk('public')->exists($img->image)) {
            Storage::disk('public')->delete($img->image);
        }
        $img->delete();

        return redirect()->route('catalogProducts.edit', $productId)->with('success', 'Image removed.');
    }

    private function storeGalleryImages(Product $product, Request $request): void
    {
        $files = $request->file('gallery_images', []);
        if (empty($files)) {
            return;
        }

        $maxSort = (int) $product->images()->max('sort_order');
        foreach ($files as $file) {
            if (! $file) {
                continue;
            }
            $path = $file->storeOptimized('images/products/gallery', 'public');
            $maxSort++;
            ProductImage::create([
                'product_id' => $product->id,
                'image' => $path,
                'sort_order' => $maxSort,
            ]);
        }
    }

}
