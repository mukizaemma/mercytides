<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\News;
use App\Models\Blogimages;
use Illuminate\Http\RedirectResponse;

class NewsController extends Controller
{
    public function index()
    {
        $blogs = News::withCount('blogimages')->latest()->paginate(10);
        return view('admin.news', [
            'blogs' => $blogs
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['news_id', 'blog_id']);

        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'image_path' => ['nullable', 'string', 'max:500'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'gallery_paths' => ['nullable', 'array'],
            'gallery_paths.*' => ['nullable', 'string', 'max:500'],
        ]);

        $countBefore = News::query()->count();

        $fileName = null;
        if ($request->hasFile('image')) {
            $fileName = $request->file('image')->storeOptimized('images/news', 'public');
        } else {
            $fileName = $this->resolveLibraryPath($request->input('image_path'));
        }

        $blog = new News();
        $blog->title = $request->input('title');
        $blog->author = $request->input('author');
        $blog->body = $request->input('body');
        $blog->image = $fileName;
        $blog->slug = $this->uniqueModelSlug(News::class, (string) $request->input('title'), null, 'news');
        $blog->published_at = null; // explicit draft
        $blog->published_by = null;
        if (Schema::hasColumn('news', 'added_by')) {
            $blog->added_by = Auth::id() ?? Auth::guard('admin')->id();
        }

        $this->assertCreatingNew($blog);
        $blog->save();

        if (News::query()->count() !== $countBefore + 1) {
            return redirect('blogs')->with('error', 'Something went wrong while saving. Existing posts were left unchanged.');
        }

        $this->attachGalleryUploads($blog, $request);
        $this->attachGalleryLibraryPaths($blog, $request->input('gallery_paths', []));

        return redirect('blogs')->with('success', 'Update saved as draft successfully');
    }

    public function edit($id)
    {
        $blog = News::with('blogimages')->findOrFail((int) $id);

        return view('admin.newsUpdate', compact('blog'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'image_path' => ['nullable', 'string', 'max:500'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'gallery_paths' => ['nullable', 'array'],
            'gallery_paths.*' => ['nullable', 'string', 'max:500'],
        ]);

        $blog = $this->findAdminRecord(News::class, $id);
        $blog->load('blogimages');
        $targetId = (int) $blog->id;

        if ($request->hasFile('image')) {
            $this->deleteOwnedNewsImage($blog->image);
            $blog->image = $request->file('image')->storeOptimized('images/news', 'public');
        } else {
            $libraryPath = $this->resolveLibraryPath($request->input('image_path'));
            if ($libraryPath && $libraryPath !== $blog->image) {
                // Don't delete a shared library asset when swapping covers.
                if ($this->isOwnedNewsUpload($blog->image)) {
                    $this->deleteOwnedNewsImage($blog->image);
                }
                $blog->image = $libraryPath;
            }
        }

        $this->attachGalleryUploads($blog, $request);
        $this->attachGalleryLibraryPaths($blog, $request->input('gallery_paths', []));

        $newTitle = (string) $request->input('title');
        if ($blog->title !== $newTitle) {
            $blog->slug = $this->uniqueModelSlug(News::class, $newTitle, $targetId, 'news');
        }
        $blog->title = $newTitle;
        $blog->author = $request->input('author');
        $blog->body = $request->input('body');

        $this->assertSameRecord($blog, $targetId);
        $blog->save();

        return redirect('blogs')->with('success', 'Update has been saved successfully');
    }

    public function destroy($id)
    {
        $blog = News::findOrFail($id);
        $isSuperAdmin = (Auth::user()->email ?? null) === 'admin@iremetech.com';
        $isOwner = !Schema::hasColumn('news', 'added_by')
            || ((int) ($blog->added_by ?? 0) === (int) (Auth::id() ?? Auth::guard('admin')->id()));
        if (! $isSuperAdmin && ! $isOwner) {
            return redirect()->back()->with('error', 'You can only delete updates that you created.');
        }
        $galleries = $blog->blogimages;
        $this->deleteOwnedNewsImage($blog->image);
        foreach ($galleries as $gallery) {
            $this->deleteOwnedNewsImage($gallery->gallery);
        }
        $blog->blogimages()->delete();
        $blog->delete();

        return back()
            ->with('success', 'Update deleted successfully');
    }

    public function publish(News $blog): RedirectResponse
    {
        $blog->published_at = now();
        $blog->published_by = auth()->id();
        $blog->save();

        return back()->with('success', 'Update published successfully');
    }

    public function unpublish(News $blog): RedirectResponse
    {
        $blog->published_at = null;
        $blog->published_by = null;
        $blog->save();

        return back()->with('warning', 'Update moved back to draft');
    }

    public function deleteBlogImage($id): RedirectResponse
    {
        $image = Blogimages::findOrFail($id);
        $this->deleteOwnedNewsImage($image->gallery);
        $image->delete();
        return back()->with('warning', 'Activity photo deleted');
    }

    private function attachGalleryUploads(News $blog, Request $request): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        foreach ($request->file('gallery') as $gallery) {
            if (! $gallery) {
                continue;
            }
            $path = $gallery->storeOptimized('images/news/gallery', 'public');
            $blog->blogimages()->create([
                'gallery' => $path,
                'news_id' => $blog->id,
            ]);
        }
    }

    /**
     * @param  array<int, string|null>|mixed  $paths
     */
    private function attachGalleryLibraryPaths(News $blog, mixed $paths): void
    {
        if (! is_array($paths)) {
            return;
        }

        $existing = $blog->blogimages()->pluck('gallery')->map(fn ($p) => ltrim((string) $p, '/'))->all();

        foreach ($paths as $raw) {
            $path = $this->resolveLibraryPath($raw);
            if ($path === null || in_array($path, $existing, true)) {
                continue;
            }
            $blog->blogimages()->create([
                'gallery' => $path,
                'news_id' => $blog->id,
            ]);
            $existing[] = $path;
        }
    }

    private function resolveLibraryPath(mixed $raw): ?string
    {
        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        $path = ltrim(str_replace('\\', '/', $raw), '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        if (! str_starts_with($path, 'images/')) {
            return null;
        }
        if (str_contains($path, '..')) {
            return null;
        }
        if (! Storage::disk('public')->exists($path)) {
            return null;
        }

        return $path;
    }

    /**
     * Only remove files that were uploaded for news (not shared media-library assets).
     */
    private function isOwnedNewsUpload(?string $path): bool
    {
        if (! is_string($path) || $path === '') {
            return false;
        }
        $path = ltrim(str_replace('\\', '/', $path), '/');

        return str_starts_with($path, 'images/news/');
    }

    private function deleteOwnedNewsImage(?string $path): void
    {
        if (! $this->isOwnedNewsUpload($path)) {
            return;
        }
        $path = ltrim(str_replace('\\', '/', (string) $path), '/');
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
