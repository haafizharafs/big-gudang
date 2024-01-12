<?php

namespace App\Http\Apis\Admin;

use App\Models\User;
use App\Models\Absen;
use App\Models\Aktivitas;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class AbsenApi extends Controller
{
    public function index(Request $request)
    {
        $query = User::select(
            'id',
            'nama',
            'foto_profil',
        );

        if ($request->filled('nama')) {
            $query->where('nama', 'LIKE', '%' . $request->nama . '%');
        }

        if ($request->filled('wilayah')) {
            $query->where('wilayah_id',  $request->wilayah);
        }

        $user = $query->orderBy('nama', 'asc')->get();

        $date = $request->date;
        $user->map(function ($user) use ($date) {
            $absen = Absen::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->first();
            $user->status = $absen ? $absen->status : 3;
            $user->absen_id = $absen ? $absen->id : 0;
            $user->absens = $absen ? $absen->getTimes() : ["", "", "", ""];
        });

        return response($user);
    }
    public function bulanan(Request $request)
    {
        $query = User::select(
            'id',
            'nama',
            'foto_profil',
        );

        if ($request->filled('nama')) {
            $query->where('nama', 'LIKE', '%' . $request->nama . '%');
        }

        if ($request->filled('wilayah')) {
            $query->where('wilayah_id',  $request->wilayah);
        }

        $user = $query->orderBy('nama', 'asc')->get();

        $m = $request->month != 12 ? (int)$request->month + 1 : 1;
        $y = $request->month != 12 ? $request->year : (int) $request->year + 1;

        $firstDate = Carbon::parse("{$request->year}-{$request->month}-1");
        $lastDate = Carbon::parse("{$y}-{$m}-0");
        $nMonth = (int) $lastDate->format('j');
        $saturday = 0;
        for ($date = $firstDate; $date->lte($lastDate); $date->addDay()) {
            if ($date->isSunday()) {
                $nMonth--;
            }
            if ($date->isSaturday()) {
                $saturday++;
            }
        }

        $user->map(function ($user) use ($request, $nMonth, $saturday) {

            $absen = Absen::where('user_id', $user->id)
                ->whereMonth('created_at', $request->month)
                ->whereYear('created_at', $request->year)
                ->get();
            $user->hadir = $absen->where('status', 1)->count() + $saturday;
            $user->terlambat = $absen->where('status', 2)->count();
            $user->nmonth =  $nMonth;
            $user->alpa = $nMonth - $user->hadir - $user->terlambat;
            return $user;
        });

        return response($user);
    }

    public function bulanan_show($id,Request $request)
    {
        try {
            $query = Absen::select('id', 'status', 'created_at')
                ->where('user_id', $id)
                ->whereYear('created_at', $request->y)
                ->whereMonth('created_at', $request->m)
                ->get();

            $absen = $query->map(function ($a) {
                $a->date = (int) $a->created_at->format('j');
                return $a;
            });

            return response($absen);
        } catch (\Throwable $th) {
            return response(['message' => $th->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'date' => 'required|date_format:Y-m-d',
            'created_at' => 'required|date_format:H:i',
            'foto' => 'required|image',
            'aktivitas' => 'required',
            'koordinat' => 'required',
            'alamat' => 'required',
        ]);
        if ($request->created_at < Absen::$settedTime[0] || $request->created_at >= Absen::$alpa) {
            return response(['errors' => ['created_at' => ['Waktu yang dipilih belum masuk atau sudah lewat waktu absen.']]], 422);
        }

        try {
            $image = $request->file('foto');
            $extension = $image->getClientOriginalExtension();
            $imageName = hash('sha512', Str::random(40) . time()) . '.' . $extension;
            $compressedImage = Image::make($image->path());
            $compressedImage->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $compressedImage->save(public_path('storage/aktivitas/' . $imageName));
            $foto = 'aktivitas/' . $imageName;
        } catch (\Throwable $th) {
            return response(['message' => $th->getMessage()], 500);
        }

        try {
            DB::transaction(function () use ($request, $foto) {
                $absen = Absen::create([
                    'user_id' => $request->id,
                    'status' => 3,
                    'created_at' => "{$request->date} {$request->created_at}",
                    'updated_at' => "{$request->date} {$request->created_at}"
                ]);

                Aktivitas::insert([
                    'absen_id' => $absen->id,
                    'user_id' => $request->id,
                    'foto' => $foto,
                    'koordinat' => $request->koordinat,
                    'alamat' => $request->alamat,
                    'aktivitas' => $request->aktivitas,
                    'created_at' => "{$request->date} {$request->created_at}",
                    'updated_at' => "{$request->date} {$request->created_at}"
                ]);
            });
        } catch (\Throwable $th) {
            if (File::exists(public_path("storage/" . $foto)) && $foto !=  'aktivitas/dummy.jpg') File::delete(public_path("storage/" . $foto));
            return response(['message' => $th->getMessage()], 500);
        }
        return response([
            'message' => 'Data Absen Berhasil Ditambah!',
            'id' => $request->id
        ]);
    }

    public function batch(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'date' => 'required|date_format:Y-m-d',
            'created_at' => 'required|date_format:H:i',
            'foto' => 'nullable|image',
            'aktivitas' => 'required',
            'koordinat' => 'required',
            'alamat' => 'required',
        ]);

        if ($request->created_at < Absen::$settedTime[0] || $request->created_at >= Absen::$alpa) {
            return response(['errors' => ['created_at' => ['Waktu yang dipilih belum masuk atau sudah lewat waktu absen.']]], 422);
        }

        $foto = 'aktivitas/dummy.jpg';
        if ($request->hasFile('foto')) {
            try {
                $image = $request->file('foto');
                $extension = $image->getClientOriginalExtension();
                $imageName = hash('sha512', Str::random(40) . time()) . '.' . $extension;
                $compressedImage = Image::make($image->path());
                $compressedImage->resize(500, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $compressedImage->save(public_path('storage/aktivitas/' . $imageName));
                $foto = 'aktivitas/' . $imageName;
            } catch (\Throwable $th) {
                return response(['message' => $th->getMessage()], 500);
            }
        }


        try {
            DB::transaction(function () use ($request, $foto) {
                $absen = Absen::create([
                    'user_id' => $request->id,
                    'status' => $request->created_at > Absen::$late ? 2 : 1,
                    'created_at' => "{$request->date} {$request->created_at}",
                    'updated_at' => "{$request->date} {$request->created_at}"
                ]);

                $data = [
                    'absen_id' => $absen->id,
                    'user_id' => $request->id,
                    'foto' => $foto,
                    'koordinat' => $request->koordinat,
                    'alamat' => $request->alamat,
                    'aktivitas' => $request->aktivitas,
                ];
                foreach (Absen::$settedTime as $i => $time) {
                    if ($i == 0) {
                        $data['created_at'] = "{$request->date} {$request->created_at}";
                        $data['updated_at'] = "{$request->date} {$request->created_at}";
                    } else {
                        $data['created_at'] = "{$request->date} {$time}";
                        $data['updated_at'] = "{$request->date} {$time}";
                    }
                    Aktivitas::insert($data);
                }
            });
        } catch (\Throwable $th) {
            if (File::exists(public_path("storage/" . $foto)) && $foto != 'aktivitas/dummy.jpg') File::delete(public_path("storage/" . $foto));
            return response(['message' => $th->getMessage()], 500);
        }
        return response([
            'message' => 'Data Absen Berhasil Ditambah!',
            'id' => $request->id
        ]);
    }
    public function show(Request $request, $id)
    {
        $user = User::select('id', 'nama')->find($id);
        $absen = Absen::select('id')
            ->where('user_id', $id)
            ->whereDate('created_at', $request->date)
            ->first();
        if ($absen) {
            $absen->aktivitass = $absen->getAktivitass();
            $user->absen = $absen;
        }
        return response($user);
    }

    public function destroy($id)
    {
        try {
            $images = Aktivitas::select('foto')->where('absen_id', $id)->pluck('foto');
            foreach ($images as $image) {
                if (File::exists(public_path("storage/" . $image) && $image !=  'aktivitas/dummy.jpg') && $image !=  'aktivitas/dummy.jpg') File::delete(public_path("storage/" . $image));
            }
            $absen = Absen::find($id);
            $absen->delete();
            return response([
                'message' => 'Data Absen Berhasil Dihapus!',
                'id' => $absen->user_id
            ]);
        } catch (\Throwable $th) {
            return response(['message' => $th->getMessage()], 500);
        }
    }
    public function export_daily(Request $request)
    {
        $query = User::select('id', 'nama');
        $users = $query->orderBy('nama', 'asc')->get();


        $data = [];
        $date = $request->tanggal;
        foreach ($users as $i => $user) {
            $absen = Absen::where('user_id', $user->id)
                ->whereDate('created_at', $date)
                ->first();
            $data[$i]['Nama'] = $user->nama;
            $data[$i]['Status'] = $absen ? $absen->getStatus() : 'Alpa';
            $aktivitass = $absen ? $absen->getAktivitass() : ['', '', '', ''];
            foreach ($aktivitass as $j => $aktivitas) {
                $data[$i]['Aktivitas'][$j]['Waktu Absen'] = $aktivitas == '' ? '' : $aktivitas->created_at->format('H:i');
                $data[$i]['Aktivitas'][$j]['Koordinat'] = $aktivitas == '' ? '' : $aktivitas->koordinat;
                $data[$i]['Aktivitas'][$j]['Alamat'] = $aktivitas == '' ? '' : $aktivitas->alamat;
            }
            // $user->status = $absen ? $absen->getStatus() : 'Alpa';
            // $user->absens =
        }
        return response($data);
    }
}
