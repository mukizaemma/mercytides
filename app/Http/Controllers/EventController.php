<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
class EventController extends Controller
{

    public function index()
    {
        $events = DB::table('events')->latest()->get();
        return view('admin.events', ['events'=>$events]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->forgetRequestRecordIds($request, ['event_id']);

        $countBefore = Event::query()->count();

        $data = new Event();
        $data->title = $request->title;
        $data->description = $request->description;
        $data->location = $request->location;
        $data->date = $request->date;
        $data->timeStart = $request->timeStart;
        $data->timeEnd = $request->timeEnd;
        $data->registerLink = $request->registerLink;
        $data->registerContact = $request->registerContact;
        $data->slug = $this->uniqueModelSlug(Event::class, (string) $request->input('title'), null, 'event');

        if ($request->hasFile('image')) {
            $data->image = $this->storeOptimizedImageBasename(
                $request->file('image'),
                'images/events',
                'public'
            );
        }

        $this->assertCreatingNew($data);
        $stored = $data->save();

        if ($stored && Event::query()->count() === $countBefore + 1) {
            return redirect('events')->with('success', 'New Event has been added successfuly');
        }

        return redirect()->back()->with('error', 'Failed to add new Event. Existing events were left unchanged.');
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
        $data = $this->findAdminRecord(Event::class, $id);

        return view('admin.eventUpdate', ['data' => $data]);
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
        $data = $this->findAdminRecord(Event::class, $id);
        $targetId = (int) $data->id;

        $newTitle = (string) $request->input('title');
        if ($data->title !== $newTitle) {
            $data->slug = $this->uniqueModelSlug(Event::class, $newTitle, $targetId, 'event');
        }

        $data->title = $newTitle;
        $data->location = $request->input('location');
        $data->date = $request->input('date');
        $data->timeStart = $request->input('timeStart');
        $data->timeEnd = $request->input('timeEnd');
        $data->registerLink = $request->input('registerLink');
        $data->registerContact = $request->input('registerContact');
        $data->status = $request->input('status');
        $data->description = $request->input('description');

        if ($request->hasFile('image') && request('image') != '') {
            $data->image = $this->storeOptimizedImageBasename(
                $request->file('image'),
                'images/events',
                'public'
            );
        }

        $this->assertSameRecord($data, $targetId);
        $data->save();

        return redirect('events')->with('success', 'Event has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = $this->findAdminRecord(Event::class, $id);
        $data->delete();

        return redirect()->back()->with('success', 'Item has been deleted');
    }
}
