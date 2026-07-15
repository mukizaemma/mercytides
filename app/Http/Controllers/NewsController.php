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

    // public function showAll()
    // {
    //     $blogs = News::latest()->paginate(10);
    //     return view('pages.blogs.home', [
    //         'blogs' => $blogs
    //     ]);
    // }

    // public function showSingle($slug)
    // {
    //     $blog = Blog::where('slug', $slug)->first();
    //     return view('pages.blogs.blog', compact('blog'));
    // }

    // public function show($id)
    // {
    //     $blog = News::find($id);
    //     return view('pages.blogs.blog', compact('blog'));
    // }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
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
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
        ]);

        $countBefore = News::query()->count();

        $fileName = '';
        if ($request->hasFile('image')) {
            $fileName = $request->file('image')->storeOptimized('images/news', 'public');
        }

        $blog = new News();
        $blog->title = $request->input('title');
        $blog->author = $request->input('author');
        $blog->body = $request->input('body');
        $blog->image = $fileName ?: null;
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

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $gallery) {
                $path = $gallery->storeOptimized('images/news/gallery', 'public');
                $blog->blogimages()->create([
                    'gallery' => $path,
                    'news_id' => $blog->id,
                ]);
            }
        }

        return redirect('blogs')->with('success', 'Blog saved as draft successfully');
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
            'gallery.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
        ]);

        $blog = $this->findAdminRecord(News::class, $id);
        $blog->load('blogimages');
        $targetId = (int) $blog->id;

        if ($request->hasFile('image')) {
            if (! empty($blog->image) && Storage::disk('public')->exists($blog->image)) {
                Storage::disk('public')->delete($blog->image);
            }
            $blog->image = $request->file('image')->storeOptimized('images/news', 'public');
        }

        // Append gallery images (don't erase existing)
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $gallery) {
                $path = $gallery->storeOptimized('images/news/gallery', 'public');
                $blog->blogimages()->create([
                    'gallery' => $path,
                    'news_id' => $blog->id,
                ]);
            }
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

        return redirect('blogs')->with('success', 'News post has been updated successfully');
    }




    public function destroy($id)
    {
        $blog = News::findOrFail($id);
        $isSuperAdmin = (Auth::user()->email ?? null) === 'admin@iremetech.com';
        $isOwner = !Schema::hasColumn('news', 'added_by')
            || ((int) ($blog->added_by ?? 0) === (int) (Auth::id() ?? Auth::guard('admin')->id()));
        if (! $isSuperAdmin && ! $isOwner) {
            return redirect()->back()->with('error', 'You can only delete blog posts that you created.');
        }
        $galleries = $blog->blogimages;
        // delete the image file
        if (!empty($blog->image) && Storage::disk('public')->exists($blog->image)) {
            Storage::disk('public')->delete($blog->image);
        }
        // delete the gallery files
        foreach($galleries as $gallery) {
            if (!empty($gallery->gallery) && Storage::disk('public')->exists($gallery->gallery)) {
                Storage::disk('public')->delete($gallery->gallery);
            }
        }
        $blog->blogimages()->delete();
        $blog->delete();

        return back()
            ->with('success', 'News deleted successfully');
    }

    public function publish(News $blog): RedirectResponse
    {
        $blog->published_at = now();
        $blog->published_by = auth()->id();
        $blog->save();

        return back()->with('success', 'News published successfully');
    }

    public function unpublish(News $blog): RedirectResponse
    {
        $blog->published_at = null;
        $blog->published_by = null;
        $blog->save();

        return back()->with('warning', 'News moved back to draft');
    }

    public function deleteBlogImage($id): RedirectResponse
    {
        $image = Blogimages::findOrFail($id);
        if (!empty($image->gallery) && Storage::disk('public')->exists($image->gallery)) {
            Storage::disk('public')->delete($image->gallery);
        }
        $image->delete();
        return back()->with('warning', 'Blog gallery image deleted');
    }

}
