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

        $request->validate(array_merge([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'gallery_paths' => ['nullable', 'array'],
            'gallery_paths.*' => ['nullable', 'string', 'max:500'],
        ], $this->imageInputRules('image')));

        $countBefore = News::query()->count();

        $fileName = $this->imageFromRequest($request, 'image', 'images/news');

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

        foreach ($this->galleryFromRequest($request, 'gallery', 'images/news/gallery') as $path) {
            $blog->blogimages()->create([
                'gallery' => $path,
                'news_id' => $blog->id,
            ]);
        }

        return redirect('blogs')->with('success', 'Update saved as draft successfully');
    }

    public function edit($id)
    {
        $blog = News::with('blogimages')->findOrFail((int) $id);

        return view('admin.newsUpdate', compact('blog'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(array_merge([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string'],
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'gallery_paths' => ['nullable', 'array'],
            'gallery_paths.*' => ['nullable', 'string', 'max:500'],
        ], $this->imageInputRules('image')));

        $blog = $this->findAdminRecord(News::class, $id);
        $blog->load('blogimages');
        $targetId = (int) $blog->id;

        $newCover = $this->imageFromRequest($request, 'image', 'images/news');
        if ($newCover && $newCover !== $blog->image) {
            $this->deleteOwnedNewsImage($blog->image);
            $blog->image = $newCover;
        }

        $existingGallery = $blog->blogimages()->pluck('gallery')->map(fn ($p) => ltrim((string) $p, '/'))->all();
        foreach ($this->galleryFromRequest($request, 'gallery', 'images/news/gallery') as $path) {
            if (in_array($path, $existingGallery, true)) {
                continue;
            }
            $blog->blogimages()->create([
                'gallery' => $path,
                'news_id' => $blog->id,
            ]);
            $existingGallery[] = $path;
        }

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
