<?php

namespace App\Http\Controllers\Admin;

use App\Models\Wilayah;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KaryawanController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->addBreadcrumb('karyawan', url('karyawan'));
        $wilayahs = Wilayah::all();
        return view('pages.admin.karyawan.index', compact('wilayahs'));
    }
}
