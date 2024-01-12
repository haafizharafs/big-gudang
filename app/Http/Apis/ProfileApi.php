<?php

namespace App\Http\Apis;


use Carbon\Carbon;
use App\Models\User;
use App\Models\Aktivitas;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class ProfileApi extends Controller
{
    public function index(Request $request)
    {
        $limit = 5;
        $offset = $limit * ($request->offset ?? 0);
        $aktivitass = Aktivitas::select(
            'id',
            'foto',
            'aktivitas',
            'created_at',
            'koordinat',
            'alamat',
        )
            ->orderBy('created_at', 'desc')
            ->where('user_id', auth()->user()->id)
            ->limit($limit)
            ->offset($offset)
            ->get();
        $aktivitass->map(function ($aktivitas) {
            $aktivitas->time = $aktivitas->created_at->translatedFormat('H:i | j M Y');
            return $aktivitas;
        });
        return response($aktivitass);
    }

    public function changePicture(Request $request)
    {
        try {
            $file = $request->file('file');
            $imageName = 'UIMG' . hash('sha512', Str::random(40) . time()) . '.' .  '.jpeg';
            $compressedImage = Image::make($file->getRealPath());
            $compressedImage->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $compressedImage->save(public_path('storage/profile/' . $imageName));
            if (File::exists(public_path("storage/" . auth()->user()->foto_profil)) && auth()->user()->foto_profil !=  'aktivitas/dummy.jpg') File::delete(public_path("storage/" . auth()->user()->foto_profil));
            $user = User::find(auth()->user()->id);
            $user->foto_profil = 'profile/' . $imageName;
            $user->save();
            return response(['message' => 'Ganti foto profil berhasil', 'foto_profil' => 'profile/' . $imageName]);
        } catch (\Throwable $th) {
            return response(['message' => 'Terjadi kesalahan saat mengganti foto', 'errors' => $th->getMessage()], 500);
        }
    }
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->user()->id,
            'no_telp' => 'required|numeric|digits_between:11,15',
        ]);
        $user = User::find(auth()->user()->id);
        $user->update($validatedData);
        return response(['message' => 'Informasi pribadi berhasil diperbarui']);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password' => 'required|min:6',
            'password_confirmation' => 'required|same:password'
        ]);
        $credentials = [
            'email' => auth()->user()->email,
            'password' => $request->password_lama
        ];
        if (!Auth::attempt($credentials)) {
            return response(['errors' => ['password_lama' => 'Password Lama Salah']], 422);
        }
        if (User::find(auth()->user()->id)->update(['password' => $request->password])) {
            return response(['message' => 'Password berhasil diubah']);
        }
        return response(['message' => 'Gagal mengubah password'], 500);
    }
}
