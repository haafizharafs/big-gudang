<?php

namespace App\Http\Controllers\Apis;


use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TeknisiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::with('wilayah:id,nama_wilayah')
            ->select('id', 'nama', 'speciality', 'foto_profil', 'no_telp', 'email', 'poin', 'wilayah_id')
            ->where('role', 2);
        if ($request->has('wilayah') && $request->wilayah != '') {
            $query->where('wilayah_id', $request->wilayah);
        }
        return response()->json($query->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|min:3',
            'speciality' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'wilayah_id' => 'required',
            'no_telp' => 'required|numeric'
        ]);
        $validatedData['role'] = 2;
        $validatedData['foto_profil'] = 'profile/dummy.png';
        User::create($validatedData);
        return response()->json([
            'status' => 200,
            'message' => 'Teknisi baru telah ditambahkan',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(User::with('wilayah', 'tims')->findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $teknisi = User::with('wilayah:id,nama_wilayah')->findOrFail($id);
        return response($teknisi);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $teknisi)
    {
        $validatedData = $request->validate([
            'nama' => 'required|min:3',
            'speciality' => 'required',
            'email' => 'required|email|unique:users,email,' . $teknisi->id,
            'wilayah_id' => 'required',
            'no_telp' => 'required|numeric'
        ]);

        $teknisi->update($validatedData);
        return response(['message' => 'Data teknisi berhasil diubah']);
    }
    public function changePassword(Request $request, User $teknisi)
    {
        $request->validate([
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);
        $credentials = [
            'email' => auth()->user()->email,
            'password' => $request->old_password
        ];
        if (Auth::attempt($credentials)) {
            if ($teknisi->update(['password' => $request->password])) {
                return response(['message' => 'Password berhasil diubah']);
            }
        }
        return response(['message' => 'Gagal mengubah password'], 423);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            User::destroy($id);
            return response([
                'message' => 'Teknisi telah dihapus',
            ]);
        } catch (\Throwable $th) {
            return response([
                'message' => 'Data dari teknisi ini sedang digunakan'
            ], 500);
        }
    }
}
