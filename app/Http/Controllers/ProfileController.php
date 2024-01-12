<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $this->addBreadcrumb('profile', url('profile'));
        return view('pages.profile.index');
    }
    public function edit()
    {
        $this->addBreadcrumb('profile', url('profile/edit'));
        return view('pages.profile.edit');
    }
}
