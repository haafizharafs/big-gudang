<?php

namespace App\Http\Apis\Admin;

use App\Models\Absen;
use App\Models\Aktivitas;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class AktivitasApi extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'date' => 'required|date_format:Y-m-d',
            'xabsen' => 'required|digits:1|numeric',
            'created_at' => 'required|date_format:H:i',
            'foto' => 'required|image',
            'aktivitas' => 'required',
            'koordinat' => 'required',
            'alamat' => 'required',
        ]);

        if ($request->xabsen == 3) {
            if ($request->created_at < Absen::$settedTime[3] || $request->created_at >= Absen::$max) {
                return response(['errors' => ['created_at' => ['Waktu yang dipilih belum masuk atau sudah lewat waktu absen.']]], 422);
            }
        } else {
            if ($request->created_at < Absen::$settedTime[$request->xabsen] || $request->created_at >=  Absen::$settedTime[$request->xabsen + 1]) {
                return response(['errors' => ['created_at' => ['Waktu yang dipilih belum masuk atau sudah lewat waktu absen.']]], 422);
            }
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
            $absen = Absen::with('aktivitass')->find($request->id);
            Aktivitas::insert([
                'absen_id' => $absen->id,
                'user_id' => $absen->user_id,
                'foto' => $foto,
                'koordinat' => $request->koordinat,
                'alamat' => $request->alamat,
                'aktivitas' => $request->aktivitas,
                'created_at' => "{$request->date} {$request->created_at}",
                'updated_at' => "{$request->date} {$request->created_at}"
            ]);
            if ($absen->aktivitass->count() + 1 < Absen::$minAbsen) {
                $absen->status = 3;
            } else {
                if ($absen->created_at->format('H:i') > Absen::$late) {
                    $absen->status = 2;
                } else {
                    $absen->status = 1;
                }
            }
            $absen->save();

            return response([
                'message' => 'Data Absen Berhasil Ditambah!',
                'id' => $absen->user_id
            ]);
        } catch (\Throwable $th) {
            if (File::exists(public_path("storage/" . $foto)) && $foto !=  'aktivitas/dummy.jpg') File::delete(public_path("storage/" . $foto));
            return response(['message' => $th->getMessage()], 500);
        }
    }
    public function update($id, Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d',
            'xabsen' => 'required|digits:1|numeric',
            'created_at' => 'required|date_format:H:i',
            'foto' => 'nullable|image',
            'aktivitas' => 'required',
            'koordinat' => 'required',
            'alamat' => 'required',
        ]);

        if ($request->xabsen == 3) {
            if ($request->created_at < Absen::$settedTime[3] || $request->created_at >= Absen::$max) {
                return response(['errors' => ['created_at' => ['Waktu yang dipilih belum masuk atau sudah lewat waktu absen.']]], 422);
            }
        } else if ($request->created_at < Absen::$settedTime[$request->xabsen] || $request->created_at >=  Absen::$settedTime[$request->xabsen + 1]) {
            return response(['errors' => ['created_at' => ['Waktu yang dipilih belum masuk atau sudah lewat waktu absen.']]], 422);
        }

        try {
            $aktivitas = Aktivitas::find($id);
            if ($request->hasFile('foto')) {
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
                if (File::exists(public_path("storage/" . $aktivitas->foto)) && $aktivitas->foto !=  'aktivitas/dummy.jpg') File::delete(public_path("storage/" . $aktivitas->foto));
                $aktivitas->foto = $foto;
                $aktivitas->save();
            }
            $aktivitas->update([
                'koordinat' => $request->koordinat,
                'alamat' => $request->alamat,
                'aktivitas' => $request->aktivitas,
                'created_at' => "{$request->date} {$request->created_at}"
            ]);

            if ($request->xabsen == 0) {
                $absen = Absen::find($aktivitas->absen_id);
                $absen->created_at = "{$request->date} {$request->created_at}";
                if ($absen->status != 3) {
                    if ($request->created_at > Absen::$late) {
                        $absen->status = 2;
                    } else {
                        $absen->status = 1;
                    }
                }
                $absen->save();
            }

            return response([
                'message' => 'Data Absen Berhasil Diubah!',
                'id' => $aktivitas->user_id
            ]);
        } catch (\Throwable $th) {
            if (File::exists(public_path("storage/" . $foto)) && $foto !=  'aktivitas/dummy.jpg') File::delete(public_path("storage/" . $foto));
            return response(['message' => $th->getMessage()], 500);
        }
    }
    public function destroy($id)
    {

        try {
            $aktivitas = Aktivitas::find($id);
            if ($aktivitas) {
                if ($aktivitas->created_at->format('H:i') < Absen::$settedTime[1]) {
                    return response(['message' => 'Aktivitas pertama tidak dapat dihapus'], 422);
                }
                if (File::exists(public_path("storage/" . $aktivitas->foto)) && $aktivitas->foto !=  'aktivitas/dummy.jpg') File::delete(public_path("storage/" .  $aktivitas->foto));
                $absen = Absen::with('aktivitass')->find($aktivitas->absen_id);
                if ($absen->aktivitass->count() + 1 < Absen::$minAbsen) $absen->status = 3;
                $absen->save();
                $aktivitas->delete();
                return response([
                    'message' => 'Data Absen Berhasil Dihapus!',
                    'id' => $absen->user_id
                ]);
            }
            return response(['message' => "Aktivitas ID {$id} tidak ditemukan!"], 500);
        } catch (\Throwable $th) {
            return response(['message' => $th->getMessage()], 500);
        }
    }
}
