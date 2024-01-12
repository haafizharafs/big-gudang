<?php

namespace App\Http\Apis\Karyawan;


use Carbon\Carbon;
use App\Models\User;
use App\Models\Absen;
use App\Models\Aktivitas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class AbsenApi extends Controller
{
    public function today()
    {
        try {
            $absen = Absen::select('id', 'status')->where('user_id', auth()->user()->id)->whereDate('created_at', now())->first();
            if ($absen) {
                $absen->aktivitass = $absen->getAktivitass();
            }
            return response($absen);
        } catch (\Throwable $th) {
            return response(['message' => $th->getMessage()], 500);
        }
    }
    public function monthly(Request $request)
    {
        try {
            $m = $request->m != 12 ? (int)$request->m + 1 : 1;
            $y = $request->m != 12 ? $request->y : (int) $request->year + 1;

            $firstDate = Carbon::parse("{$request->y}-{$request->m}-1");
            $lastDate = Carbon::parse("{$y}-{$m}-0");
            $nMonth = (int) $lastDate->format('j');
            $saturday = 0;
            for ($date = $firstDate; $date->lte($lastDate); $date->addDay()) {
                if ($date->isSunday()) {
                    $nMonth--;
                }
                if (auth()->user()->id == 72) {
                    if ($date->isSaturday()) {
                        $saturday++;
                    }
                }
            }
            $query = Absen::select('id', 'status', 'created_at')
                ->where('user_id', auth()->user()->id)
                ->whereYear('created_at', $request->y)
                ->whereMonth('created_at', $request->m)
                ->get();

            $h = $query->where('status', 1)->count();
            if (auth()->user()->id == 72) {
                $h += $saturday;
            }
            $t = $query->where('status', 2)->count();
            $a = $nMonth - $h - $t;
            $absen = $query->map(function ($a) {
                $a->date = (int) $a->created_at->format('j');
                return $a; // Return the modified $a
            });

            $data = [
                'h' => $h,
                't' => $t,
                'a' => $a,
                'absens' => $absen,
            ];

            return response($data);
        } catch (\Throwable $th) {
            return response(['message' => $th->getMessage()], 500);
        }
    }
    public function index(Request $request)
    {
        $id = auth()->user()->id;
        $query = Absen::where('user_id', $id);

        if ($request->has('month') && !empty($request->month)) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->has('year') && !empty($request->year)) {
            $query->whereYear('created_at', $request->year);
        }

        $absens = $query->get();
        $absens->map(function ($absen) {
            $absen->tanggalFormat = Carbon::parse($absen->created_at)->translatedFormat("l, j F Y");
            $absen->absens = $absen->getTimes();
            $absen->status = $absen->getStatus();
            return $absen;
        });
        return response()->json($absens);
    }
    public function show($id)
    {
        $absen = Absen::select('id', 'status', 'created_at')->find($id);
        $absen->created_atFormat = $absen->created_at->translatedFormat('l, j F Y');
        $absen->aktivitass = $absen->getAktivitass();
        return response($absen);
    }
}
