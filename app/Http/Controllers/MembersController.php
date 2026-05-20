<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Member;
use App\Models\Ministry;
use App\Models\Branch;
use App\Models\Program;
use Illuminate\Support\Facades\Schema;

class MembersController extends Controller
{
    public function AllMembers(){
        $members = Member::with('branch')->get();
        $programs = Schema::hasTable('programs')
            ? Program::query()->oldest()->get()
            : collect();

        return view('admin.members', [
            'members' => $members,
            'programs' => $programs,
        ]);
    }

    public function saveMemb(Request $request){
        $data = new Member();
        $data->names = $request->names;
        $data ->phone = $request->phone;
        $data ->address = $request->address;
        $data ->gender = $request->gender;
        $data ->martual = $request->martual;
        $data ->membership = $request->membership;
        $data ->dateJoined = $request->dateJoined;

        $stored = $data->save();

        if($stored){
            return redirect()->back()->with('success', 'Thank you for your membership. We will get back to you soon for more details');
        }
    }
}
