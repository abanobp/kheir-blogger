<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

use App\Http\Requests;

class VolunteerController extends Controller
{
    /**
     * Passes User's id to volunteer's view
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public static function show($id)
    {

        $volunteer = User::findOrFail($id);
        return view('volunteer.show', compact('volunteer'));
    }

}