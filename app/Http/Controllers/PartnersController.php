<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class PartnersController extends Controller
{
    public function index()
    {
        return view('admin.partners', [
            'data' => Partner::query()->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'names' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
        ]);

        $partner = new Partner();
        $partner->names = $validated['names'];
        $partner->website = $validated['website'] ?? null;
        $partner->description = $validated['description'] ?? null;
        $partner->slug = Str::slug($validated['names']) ?: ('partner-'.Str::random(6));

        try {
            $partner->image = $this->storePartnerLogo($request->file('image'));
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Could not upload the partner logo. Please use a JPG, PNG, GIF, or WEBP image under 4MB.');
        }

        $partner->save();

        return redirect()
            ->route('partner')
            ->with('success', 'Partner has been added successfully.');
    }

    public function edit($id)
    {
        $data = Partner::query()->findOrFail($id);

        return view('admin.partnerUpdate', ['data' => $data]);
    }

    public function update(Request $request, $id)
    {
        $partner = Partner::query()->findOrFail($id);

        $validated = $request->validate([
            'names' => ['required', 'string', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:4096'],
        ]);

        $partner->names = $validated['names'];
        $partner->website = $validated['website'] ?? null;
        $partner->description = $validated['description'] ?? null;

        if ($request->hasFile('image')) {
            try {
                $oldImage = $partner->image;
                $partner->image = $this->storePartnerLogo($request->file('image'));
                $this->deletePartnerLogo($oldImage);
            } catch (Throwable $e) {
                report($e);

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Could not upload the partner logo. Please use a JPG, PNG, GIF, or WEBP image under 4MB.');
            }
        }

        $partner->save();

        return redirect()
            ->route('partner')
            ->with('success', 'Partner has been updated.');
    }

    public function destroy($id)
    {
        $partner = Partner::query()->findOrFail($id);
        $this->deletePartnerLogo($partner->image);
        $partner->delete();

        return redirect()
            ->route('partner')
            ->with('success', 'Partner has been deleted.');
    }

    protected function storePartnerLogo($file): string
    {
        $basename = $this->storeOptimizedImageBasename(
            $file,
            'images/partners',
            'public',
            ['preset' => 'logo']
        );

        return ltrim((string) $basename, '/');
    }

    protected function deletePartnerLogo(?string $image): void
    {
        if (empty($image)) {
            return;
        }

        $path = ltrim($image, '/');
        if (! str_contains($path, '/')) {
            $path = 'images/partners/'.$path;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
