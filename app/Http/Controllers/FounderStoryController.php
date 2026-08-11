<?php

namespace App\Http\Controllers;

use App\Models\FounderStory;
use Illuminate\Http\Request;

class FounderStoryController extends Controller
{
    public function update(Request $request)
    {
        $request->validate(array_merge([
            'title' => ['nullable', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'header_caption' => ['nullable', 'string', 'max:500'],
            'founder_name' => ['nullable', 'string', 'max:255'],
            'founder_role' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ], $this->imageInputRules('founder_image')));

        $story = FounderStory::firstOrSingleton();
        $story->title = $request->input('title');
        $story->tagline = $request->input('tagline');
        $story->header_caption = $request->input('header_caption');
        $story->founder_name = $request->input('founder_name');
        $story->founder_role = $request->input('founder_role');
        $story->content = $request->input('content');

        $image = $this->imageFromRequest($request, 'founder_image', 'images/founder', ['preset' => 'portrait']);
        if ($image) {
            $story->founder_image = $image;
        }

        $story->save();

        return redirect()
            ->route('about', ['tab' => 'founder-story'])
            ->with('success', 'Founder\'s story has been updated successfully.');
    }
}
