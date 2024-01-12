<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AbsenController extends Controller
{
    public function index() {
        $this->addBreadcrumb('absensi', url('admin/absen'));
        $this->addBreadcrumb('harian', url('admin/absen'));
        return view('pages.admin.absen.index');
    }
    public function bulanan() {
        $this->addBreadcrumb('absensi', url('admin/absen'));
        $this->addBreadcrumb('bulanan', url('admin/absen/bulanan'));
        return view('pages.admin.absen.bulanan');
    }
}
