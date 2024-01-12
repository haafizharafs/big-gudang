<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;

class AbsenController extends Controller
{
    public function __invoke() {
        $this->addBreadcrumb('absen', url('absen'));
        return view('pages.karyawan.absen.index');
    }
}
