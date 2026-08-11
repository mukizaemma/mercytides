<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        Team::ensureLeadershipSeeded();
        Team::ensureImageFocusColumn();

        $members = Team::query()->orderBy('id')->get();

        return view('admin.team', ['members' => $members]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['staff_id', 'team_id']);

        $request->validate(array_merge([
            'names' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'image_focus' => ['nullable', 'string', 'max:32'],
        ], $this->imageInputRules('image', required: true)));

        $countBefore = Team::query()->count();

        $data = new Team();
        $data->names = $request->names;
        $data->phone = $request->phone;
        $data->position = $request->position;
        $data->category = $request->category;
        $data->facebook = $request->facebook;
        $data->instagram = $request->instagram;
        $data->twitter = $request->twitter;
        $data->bio = $request->bio;
        if (Team::ensureImageFocusColumn()) {
            $data->image_focus = Team::normalizeImageFocus($request->input('image_focus'));
        }

        $image = $this->imageFromRequest($request, 'image', 'images/staff', ['preset' => 'portrait']);
        if ($image) {
            $data->image = $image;
        } else {
            return redirect()->back()->with('error', 'Image is required.');
        }

        $this->assertCreatingNew($data);
        $stored = $data->save();

        if ($stored && Team::query()->count() === $countBefore + 1) {
            return redirect('staff')->with('success', 'New Staff has been added successfuly');
        }

        return redirect()->back()->with('error', 'Failed to add new Staff. Existing staff were left unchanged.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->findAdminRecord(Team::class, $id);
        Team::ensureImageFocusColumn();

        return view('admin.teamUpdate', ['data' => $data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate(array_merge([
            'names' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'facebook' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'image_focus' => ['nullable', 'string', 'max:32'],
        ], $this->imageInputRules('image')));

        $data = $this->findAdminRecord(Team::class, $id);
        $targetId = (int) $data->id;

        $data->names = $request->input('names');
        $data->phone = $request->input('phone');
        $data->position = $request->input('position');
        $data->category = $request->input('category');
        $data->facebook = $request->input('facebook');
        $data->instagram = $request->input('instagram');
        $data->twitter = $request->input('twitter');
        $data->bio = $request->input('bio');
        if (Team::ensureImageFocusColumn()) {
            $data->image_focus = Team::normalizeImageFocus($request->input('image_focus'));
        }

        $image = $this->imageFromRequest($request, 'image', 'images/staff', ['preset' => 'portrait']);
        if ($image) {
            $data->image = $image;
        }

        $this->assertSameRecord($data, $targetId);
        $data->save();

        return redirect('staff')->with('success', 'Staff Members has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->findAdminRecord(Team::class, $id);
        $data->delete();

        return redirect()->back()->with('success', 'Staff has been deleted');
    }
}
