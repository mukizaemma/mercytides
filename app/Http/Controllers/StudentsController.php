<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Students;

class StudentsController extends Controller
{
    public function index()
    {

        $data = DB::table('students')->latest()->get();
        return view('admin.students', ['data'=>$data]);
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


    public function store(Request $request)
    {
        $request->validate(array_merge([
            'names' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'testimony' => ['nullable', 'string'],
        ], $this->imageInputRules('image')));

        $data = new Students();
        $data->names = $request->names;
        $data ->address = $request->address;
        $data ->phone = $request->phone;
        $data ->testimony = $request->testimony;

        $image = $this->imageFromRequest($request, 'image', 'images/students', ['preset' => 'portrait']);
        if ($image) {
            $data->image = $image;
        }

        $stored = $data->save();

        if($stored){
            return redirect('students')->with('success', 'New Student has been added successfuly');
        }

        return redirect()->back()->with('error','Failed to add new Partner');
    }


    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $student = Students::find($id);
        return view('admin.studentUpdate', ['student'=>$student]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(array_merge([
            'names' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'testimony' => ['nullable', 'string'],
        ], $this->imageInputRules('image')));

        $data = Students::find($id);
        $data->names = $request->input('names');
        $data->phone = $request->input('phone');
        $data->address = $request->input('address');
        $data->testimony = $request->input('testimony');

        if(!$data){
            return back()->with('Error','Student Not Found');
        }

        $image = $this->imageFromRequest($request, 'image', 'images/students', ['preset' => 'portrait']);
        if ($image) {
            $data->image = $image;
        }

        $data->update();

        return redirect('students')->with('success','Student has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Students::find($id);
        $data->delete($id);
        return redirect()->back()->with('success', 'Item has been deleted');
    }
}
