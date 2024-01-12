<?php

namespace App\Http\Apis;


use App\Models\Aktivitas;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Absen;
use App\Models\User;
use App\Models\Wilayah;

class DashboardApi extends Controller
{
    public function index(Request $request)
    {
        $limit = 5;
        $offset = $limit * ($request->offset ?? 0);
        $aktivitass = Aktivitas::select(
            'aktivitas.id',
            'users.nama',
            'users.foto_profil',
            'users.speciality',
            'aktivitas.foto',
            'aktivitas.aktivitas',
            'aktivitas.created_at',
            'aktivitas.koordinat',
            'aktivitas.alamat',
        )
            ->leftJoin('users', 'aktivitas.user_id', '=', 'users.id')
            ->whereDate('aktivitas.created_at', now())
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get();
        $aktivitass->map(function ($aktivitas) {
            $aktivitas->time = $aktivitas->created_at->format('H:i');
            return $aktivitas;
        });
        return response($aktivitass);
    }
    public function recap()
    {
        $wilayahs = Wilayah::select('id','nama_wilayah')
        ->whereHas('users')
        ->get();
        $wilayahs->map(function ($wilayah){

            $users = Absen::select(
                'absens.id',
                'users.nama',
                'users.foto_profil',
            )
            ->leftJoin('users','user_id','=','users.id')
            ->where('users.wilayah_id',$wilayah->id)
            ->orderBy('users.nama','asc')
            ->whereDate('absens.created_at',now())
            ->get();

            $wilayah->hadir = $users->count();
            $wilayah->all = User::where('wilayah_id',$wilayah->id)->count();
            $users->map(function ($user) {
                $user->aktivitass = $user->getTimes();
                return $user;
            });
            $wilayah->users = $users;
            return $wilayah;
        });
        return $wilayahs;
    }
}
