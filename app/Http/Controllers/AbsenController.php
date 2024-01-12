<?php

namespace App\Http\Controllers;

use App\Models\Absen;
use App\Models\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AbsenController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->addBreadcrumb('absen', url('absen'));
        $absen = \App\Models\Absen::where('user_id', auth()->user()->id)
            ->whereDate('created_at', date('Y-m-d'))
            ->first();

        if ($absen) {
            if ($absen->isAbsen()) {
                return view('pages.absen');
            }
            return redirect(url('karyawan/absen'));
        }

        if (date('H:i') >= \App\Models\Absen::$settedTime[0]  && date('H:i') < \App\Models\Absen::$alpa) {
            return view('pages.absen');
        }

        return redirect(url('karyawan/absen'));
    }
}
