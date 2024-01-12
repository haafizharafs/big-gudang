<?php

namespace App\Http\Apis;


use App\Models\Absen;
use App\Models\Aktivitas;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AbsenApi extends Controller
{
    public function __invoke(Request $request)
    {
        $id = auth()->user()->id;
        $request->validate([
            'foto' => 'required',
            'aktivitas' => 'required',
            'alamat' => 'required',
            'koordinat' => 'required',
        ], [
            'foto.required' => 'Foto harus diisi',
            'aktivitas.required' => 'Aktivitas harus diisi.',
            'koordinat.required' => 'Izinkan website mengakses GPS.',
            'alamat.required' => 'Izinkan website mengakses GPS.',
        ]);

        $img = $request->foto;
        list($type, $image_parts) = explode(';', $img);
        list(, $image_parts) = explode(',', $image_parts);
        $extension = explode('/', $type)[1];
        $image_base64 = base64_decode($image_parts);
        $imageName = 'aktivitas/' . hash('sha512', Str::random(40) . time()) . '.' . $extension;
        file_put_contents(public_path('storage/' . $imageName), $image_base64);

        $absen =  Absen::where('user_id', $id)->whereDate('created_at', date('Y-m-d'))->first();

        if (!$absen) {
            if (now()->format('H:i') >= Absen::$alpa) {
                return response(['message' => 'Waktu absen telah lewat'], 403);
            }
            $absen = new Absen;
            $absen->user_id = $id;
        } else {
            if (Aktivitas::where('absen_id', $absen->id)->count() >= Absen::$minAbsen - 1) {
                $absen->status = $absen->created_at->format('H:i') > Absen::$late ? 2 : 1;
            }
        }
        $absen->save();

        $data = [
            'absen_id' => $absen->id,
            'user_id' => auth()->user()->id,
            'foto' => $imageName,
            'koordinat' => $request->koordinat,
            'alamat' => $request->alamat,
            'aktivitas' => $request->aktivitas,
        ];

        Aktivitas::create($data);
        return response(['message' => 'Absen berhasil']);
    }
}
