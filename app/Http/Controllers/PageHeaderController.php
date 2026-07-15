<?php

namespace App\Http\Controllers;

use App\Models\PageHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageHeaderController extends Controller
{
    public function index()
    {
        PageHeader::ensureCatalog();

        $catalogKeys = array_keys(PageHeader::catalog());

        // Live public pages from the catalog, plus any admin-created custom headers.
        $headers = PageHeader::query()
            ->ordered()
            ->where(function ($query) use ($catalogKeys) {
                $query->whereIn('page_key', $catalogKeys)
                    ->orWhere('page_key', 'like', 'custom_%');
            })
            ->get();

        $default = $headers->firstWhere('is_default', true)
            ?: PageHeader::defaultHeader();

        return view('admin.page-headers', compact('headers', 'default'));
    }

    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['header_id', 'page_header_id']);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'page_key' => ['nullable', 'string', 'max:64', 'regex:/^[a-z0-9_\-]+$/', 'unique:page_headers,page_key'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:8192'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $countBefore = PageHeader::query()->count();

        $key = trim((string) ($validated['page_key'] ?? ''));
        if ($key === '') {
            $key = Str::slug($validated['label'], '_');
        }

        if ($key === '' || PageHeader::query()->where('page_key', $key)->exists()) {
            $key = 'custom_'.Str::lower(Str::random(6));
        }

        $header = new PageHeader();
        $header->page_key = $key;
        $header->label = $validated['label'];
        $header->sort_order = ((int) PageHeader::query()->max('sort_order')) + 10;
        $header->image = $request->file('image')->storeOptimized('images/page-headers', 'public', ['preset' => 'hero']);
        $header->is_default = false;

        $this->assertCreatingNew($header);
        $header->save();

        if (PageHeader::query()->count() !== $countBefore + 1) {
            return redirect()
                ->route('pageHeaders.index')
                ->with('error', 'Something went wrong while saving. Existing page headers were left unchanged.');
        }

        if ($request->boolean('is_default')) {
            $this->markDefault($header);
        }

        return redirect()->route('pageHeaders.index')
            ->with('success', 'Page header has been added.');
    }

    public function update(Request $request, $id)
    {
        $header = $this->findAdminRecord(PageHeader::class, $id);
        $targetId = (int) $header->id;

        $isBuiltIn = array_key_exists($header->page_key, PageHeader::catalog());

        $rules = [
            'label' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:8192'],
            'is_default' => ['nullable', 'boolean'],
        ];

        if (! $isBuiltIn) {
            $rules['page_key'] = [
                'nullable',
                'string',
                'max:64',
                'regex:/^[a-z0-9_\-]+$/',
                Rule::unique('page_headers', 'page_key')->ignore($header->id),
            ];
        }

        $validated = $request->validate($rules);

        $header->label = $validated['label'];

        if (! $isBuiltIn && ! empty($validated['page_key'])) {
            $header->page_key = $validated['page_key'];
        }

        if ($request->hasFile('image')) {
            $this->deleteImageFile($header->image);
            $header->image = $request->file('image')->storeOptimized('images/page-headers', 'public', ['preset' => 'hero']);
        }

        $this->assertSameRecord($header, $targetId);
        $header->save();

        if ($request->boolean('is_default')) {
            $this->markDefault($header);
        }

        return redirect()->route('pageHeaders.index')
            ->with('success', 'Page header has been updated.');
    }

    public function clearImage($id)
    {
        $header = $this->findAdminRecord(PageHeader::class, $id);
        $this->deleteImageFile($header->image);
        $header->image = null;
        $header->save();

        return redirect()->route('pageHeaders.index')
            ->with('success', 'Page header image cleared. This page will use the site default.');
    }

    public function setDefault($id)
    {
        $header = $this->findAdminRecord(PageHeader::class, $id);
        if (empty($header->image)) {
            return redirect()->route('pageHeaders.index')
                ->with('error', 'Upload an image before setting this page header as the site default.');
        }

        $this->markDefault($header);

        return redirect()->route('pageHeaders.index')
            ->with('success', 'Default page header updated.');
    }

    public function destroy($id)
    {
        $header = $this->findAdminRecord(PageHeader::class, $id);

        if (array_key_exists($header->page_key, PageHeader::catalog())) {
            return redirect()->route('pageHeaders.index')
                ->with('error', 'Built-in page headers cannot be deleted. Clear the image instead.');
        }

        $this->deleteImageFile($header->image);
        $header->delete();

        if (! PageHeader::query()->where('is_default', true)->exists()) {
            PageHeader::query()->where('page_key', PageHeader::DEFAULT_KEY)->update(['is_default' => true]);
        }

        return redirect()->route('pageHeaders.index')
            ->with('success', 'Custom page header removed.');
    }

    protected function markDefault(PageHeader $header): void
    {
        PageHeader::query()->where('is_default', true)->update(['is_default' => false]);
        $header->is_default = true;
        $header->save();
    }

    protected function deleteImageFile(?string $image): void
    {
        if (empty($image)) {
            return;
        }

        $path = ltrim($image, '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
