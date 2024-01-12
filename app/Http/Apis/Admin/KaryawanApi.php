<?php

namespace App\Http\Apis\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class KaryawanApi extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::with('wilayah:id,nama_wilayah')
                ->select(
                    'id',
                    'nama',
                    'foto_profil',
                    'role',
                    'speciality',
                    'email',
                    'no_telp',
                    'wilayah_id'
                )
                ->whereNot('role', 0);
            if ($request->filled('wilayah_id')) {
                $query->where('wilayah_id', $request->wilayah);
            }
            if ($request->filled('nama')) {
                $query->where('nama', 'LIKE', '%' . $request->nama . '%');
            }

            $orderBy = explode(',', $request->order_by);
            $query->orderBy($orderBy[0], $orderBy[1]);

            return response($query->orderBy('nama', 'asc')->get());
        } catch (\Throwable $th) {
            return response(['message' => 'Gagal', 'errors' => $th->getMessage()], 500);
        }
    }
    public function store(Request $request)
    {

        $request->validate([
            'nama' => 'required|max:16',
            'speciality' => 'required|max:32',
            'role' => 'required|numeric|digits:1',
            'wilayah_id' => 'required|exists:wilayahs,id',
            'email' => 'required|email|unique:users,email',
            'no_telp' => 'required|min:11|max:15',
            'password' => 'required|min:6',
        ]);
        try {
            User::create([
                'nama' => ucwords($request->nama),
                'speciality' => ucwords($request->speciality),
                'role' => $request->role,
                'wilayah_id' => $request->wilayah_id,
                'email' => $request->email,
                'no_telp' => $request->no_telp,
                'password' => $request->password,
            ]);
            return response(['message' => "Karyawan {$request->nama} berhasil ditambahkan"]);
        } catch (\Throwable $th) {
            return response(['message' => 'Gagal dalam menambahkan data', 'errors' => $th->getMessage()], 500);
        }
    }
    public function update($id, Request $request)
    {
        $request->validate([
            'nama' => 'required|max:16',
            'speciality' => 'required|max:32',
            'role' => 'required|numeric|digits:1',
            'wilayah_id' => 'required|exists:wilayahs,id',
            'email' => 'required|email|unique:users,email,' . $id,
            'no_telp' => 'required|min:11|max:15',
            'password' => 'nullable|min:6',
        ]);
        try {
            $user = User::find($id);
            $user->update([
                'nama' => ucwords($request->nama),
                'speciality' => ucwords($request->speciality),
                'role' => $request->role,
                'wilayah_id' => $request->wilayah_id,
                'email' => $request->email,
                'no_telp' => $request->no_telp,
            ]);
            if ($request->filled('password')) {
                $user->update([
                    'password' => $request->password,
                ]);
            }

            return response(['message' => "Karyawan {$request->nama} berhasil ditambahkan"]);
        } catch (\Throwable $th) {
            return response(['message' => 'Gagal dalam menambahkan data', 'errors' => $th->getMessage()], 500);
        }
    }
    public function delete($id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            return response(['message' => 'Berhasil menghapus data karyawan']);
        } catch (\Throwable $th) {
            return response(['message' => 'Gagal menghapus data karyawan!', 'errors' => $th->getMessage()], 500);
        }
    }
}
