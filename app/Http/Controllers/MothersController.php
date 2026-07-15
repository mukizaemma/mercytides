<?php

namespace App\Http\Controllers;

use App\Models\Mother;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class MothersController extends Controller
{
    public function index()
    {
        $mothers = Mother::latest()->get();

        return view('admin.mothers', compact('mothers'));
    }

    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['mother_id']);

        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:8192'],
        ]);

        $countBefore = Mother::query()->count();

        $mother = new Mother();
        $mother->name = $request->input('name');
        $mother->age = $request->input('age');
        $mother->description = $request->input('description');
        $mother->vision = $request->input('vision');

        if (Schema::hasColumn('mothers', 'added_by')) {
            $mother->added_by = Auth::id() ?? Auth::guard('admin')->id();
        }

        if ($request->hasFile('image')) {
            $mother->image = $request->file('image')->storeOptimized('images/mothers', 'public', ['preset' => 'portrait']);
        }

        $this->assertCreatingNew($mother);
        $mother->save();

        if (Mother::query()->count() !== $countBefore + 1) {
            return redirect()
                ->route('mothers.index')
                ->with('error', 'Something went wrong while saving. Existing mother profiles were left unchanged.');
        }

        return redirect()->route('mothers.index')->with('success', 'Mother profile has been added successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'age' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'vision' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:8192'],
        ]);

        $mother = $this->findAdminRecord(Mother::class, $id);
        $targetId = (int) $mother->id;
        $mother->name = $request->input('name');
        $mother->age = $request->input('age');
        $mother->description = $request->input('description');
        $mother->vision = $request->input('vision');

        if ($request->hasFile('image')) {
            if (! empty($mother->image) && Storage::disk('public')->exists($mother->image)) {
                Storage::disk('public')->delete($mother->image);
            }
            $mother->image = $request->file('image')->storeOptimized('images/mothers', 'public', ['preset' => 'portrait']);
        }

        $this->assertSameRecord($mother, $targetId);
        $mother->save();

        return redirect()->route('mothers.index')->with('success', 'Mother profile has been updated.');
    }

    public function destroy($id)
    {
        $mother = $this->findAdminRecord(Mother::class, $id);
        $isSuperAdmin = (Auth::user()->email ?? null) === 'admin@iremetech.com';
        $isOwner = ! Schema::hasColumn('mothers', 'added_by')
            || ((int) ($mother->added_by ?? 0) === (int) (Auth::id() ?? Auth::guard('admin')->id()));

        if (! $isSuperAdmin && ! $isOwner) {
            return redirect()->back()->with('error', 'You can only delete mother profiles that you created.');
        }

        if (! empty($mother->image) && Storage::disk('public')->exists($mother->image)) {
            Storage::disk('public')->delete($mother->image);
        }

        $mother->delete();

        return redirect()->back()->with('success', 'Mother profile has been deleted.');
    }
}
