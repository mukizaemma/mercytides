<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * Legacy Young Mothers gallery admin — merged into Sponsorship profiles (young_mother).
 */
class MothersController extends Controller
{
    public function index()
    {
        return redirect()
            ->route('sponsorship.index', ['type' => 'young_mother'])
            ->with('success', 'Mother profiles are managed under Sponsorship profiles. Showing young mothers.');
    }

    public function store(Request $request)
    {
        return $this->index();
    }

    public function update(Request $request, $id)
    {
        return $this->index();
    }

    public function destroy($id)
    {
        return $this->index();
    }
}
