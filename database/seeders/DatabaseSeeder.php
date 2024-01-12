<?php

namespace Database\Seeders;

use App\Models\Absen;
use App\Models\Aktivitas;
use App\Models\Tim;
use App\Models\User;
use App\Models\Paket;
use App\Models\Pemasangan;
use App\Models\TimAnggota;
use App\Models\JenisGangguan;
use App\Models\JenisPekerjaan;
use App\Models\Kesulitan;
use App\Models\Pekerjaan;
use App\Models\Wilayah;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $wilayah = [
            'Pontianak',
            'Sintang',
            'Ngabang'
        ];

        foreach ($wilayah as $key => $value) {
            Wilayah::create([
                'nama_wilayah' => $value,
                'ket' => '-'
            ]);
        }

        User::create(
            [
                'nama' => 'Admin',
                'speciality' => 'Admin',
                'role' => 1,
                'email' => 'admin@bigindonesia.site',
                'email_verified_at' => now(),
                'password' => '123',
                'foto_profil' => 'profile/kamisato.webp',
                'wilayah_id' => 1,
                'no_telp' => "6281521544674",
            ]
        );
        User::create(
            [
                'nama' => 'Muhamad Ardalepa',
                'speciality' => 'Admin',
                'role' => 2,
                'email' => 'ardalepa@bigindonesia.site',
                'email_verified_at' => now(),
                'password' => 'sumeru',
                'foto_profil' => 'profile/Nilou.webp',
                'wilayah_id' => 1,
                'no_telp' => "6281521544674",
            ]
        );
    }
}
