<?php

namespace App\Http\Controllers;

use App\Models\Campain;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CampainsController extends Controller
{
    public function index()
    {
        $campain = Campain::latest()->get();
        $programs = Program::all();

        return view('admin.campains', ['campain' => $campain, 'programs' => $programs]);
    }

    public function store(Request $request)
    {
        // Create must never upsert/overwrite by slug.
        $this->forgetRequestRecordIds($request, ['campain_id', 'campaign_id']);

        $countBefore = Campain::query()->count();

        $fileName = null;
        if ($request->hasFile('image')) {
            $fileName = $this->storeOptimizedImageBasename(
                $request->file('image'),
                'images/campaigns',
                'public'
            );
        }

        $title = (string) $request->input('title');
        $slug = $this->uniqueModelSlug(Campain::class, $title, null, 'campaign');

        $campaign = new Campain();
        $campaign->title = $title;
        $campaign->goal = $request->input('goal');
        $campaign->description = $request->input('description');
        $campaign->short_description = $request->input('short_description');
        $campaign->call_to_action = $request->input('call_to_action');
        $campaign->donation_url = $request->input('donation_url');
        $campaign->youtube_video = $request->input('youtube_video');
        $campaign->program_id = $request->input('program_id');
        $campaign->status = $request->input('status', 'active');
        $campaign->start_date = $request->input('start_date');
        $campaign->end_date = $request->input('end_date');
        $campaign->image = $fileName;
        $campaign->target_people = $request->input('target_people');
        $campaign->cost_per_person = $request->input('cost_per_person');
        $campaign->slug = $slug;

        $this->assertCreatingNew($campaign);
        $campaign->save();

        if (Campain::query()->count() !== $countBefore + 1) {
            return redirect()
                ->route('campainCrud')
                ->with('error', 'Something went wrong while saving. Existing campaigns were left unchanged.');
        }

        return redirect()->route('campainCrud')->with('success', 'New Campaign has been added successfully');
    }

    public function edit($id)
    {
        $campain = $this->findAdminRecord(Campain::class, $id);
        $programs = Program::all();

        return view('admin.campainUpdate', ['campain' => $campain, 'programs' => $programs]);
    }

    public function update(Request $request, $id)
    {
        $post = $this->findAdminRecord(Campain::class, $id);
        $targetId = (int) $post->id;

        if ($request->hasFile('image')) {
            $fileName = $this->storeOptimizedImageBasename(
                $request->file('image'),
                'images/campains',
                'public'
            );
            if (! empty($post->image)) {
                Storage::delete('public/images/campains/'.$post->image);
            }
            $post->image = $fileName;
        }

        if ($request->hasFile('youtubeimg')) {
            $fileName = $this->storeOptimizedImageBasename(
                $request->file('youtubeimg'),
                'images/campains',
                'public'
            );
            if (! empty($post->youtubeimg)) {
                Storage::delete('public/images/campains/'.$post->youtubeimg);
            }
            $post->youtubeimg = $fileName;
        }

        $newTitle = (string) $request->input('title');
        if ($post->title !== $newTitle) {
            $post->slug = $this->uniqueModelSlug(Campain::class, $newTitle, $targetId, 'campaign');
        }

        $post->title = $newTitle;
        $post->description = $request->input('description');
        $post->short_description = $request->input('short_description');
        $post->donation_url = $request->input('donation_url');
        $post->program_id = $request->input('program_id');

        $this->assertSameRecord($post, $targetId);
        $updated = $post->save();

        if ($updated) {
            return redirect()->route('campainCrud')->with('success', 'Campaign updated successfully');
        }

        return redirect()->route('campainCrud')->with('error', 'Something Went Wrong');
    }

    public function updateRaised(Request $request, $id)
    {
        $campaign = $this->findAdminRecord(Campain::class, $id);
        $raised = $campaign->raised + $request->raised;
        $percentage = $campaign->goal ? round(($raised / $campaign->goal) * 100) : 0;
        $campaign->raised = $raised;
        $campaign->percentage = $percentage;
        $campaign->save();

        return redirect()->back()->with('success', 'Raised amount updated successfully.');
    }

    public function resetGoalRaised(Request $request, $id)
    {
        $campain = $this->findAdminRecord(Campain::class, $id);
        $campain->raised = null;
        $campain->percentage = null;
        $campain->goal = null;
        $campain->save();

        return redirect()->back()->with('success', 'Goal and Raised amounts reset successfully');
    }

    public function destroy($id)
    {
        $post = $this->findAdminRecord(Campain::class, $id);

        if (! empty($post->image)) {
            Storage::delete('public/images/campains/'.$post->image);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Campaign Deleted');
    }
}
