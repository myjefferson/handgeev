<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function users()
    {
        $id_user = Auth::user()->id;
        $user = User::where(['id' => $id_user])->first();
        return view('pages.dashboard.user.index', compact(['user']));
    }

    public function plans()
    {
        $id_user = Auth::user()->id;
        $user = User::where(['id' => $id_user])->first();
        return view('pages.dashboard.user.index', compact(['user']));
    }
}
