<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;


class TeknisiController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 1) {
            $this->addBreadcrumb('teknisi', route('teknisi'));
            $wilayahs = \App\Models\Wilayah::all();
            return view('pages.admin.teknisi.index', compact('wilayahs'));
        }
        abort(404);
    }
    public function show($id)
    {
        $this->addBreadcrumb('teknisi', route('teknisi.show',$id));
        $user = User::with('wilayah')->find($id);
        return view('pages.admin.teknisi.teknisi-show',compact('user'));
    }
}
