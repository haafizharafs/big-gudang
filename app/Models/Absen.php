<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Absen extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public static $minAbsen = 3;
    public static $late = '08:55';
    public static $alpa = '09:30';
    public static $max = '17:00';
    public static $settedTime =  [
        '07:15',
        '11:00',
        '13:30',
        '16:00'
    ];

    public function getTimes()
    {
        foreach ($this::$settedTime as $i => $time) {
            $aktivitas[$i] = '';
        }
        foreach (Aktivitas::select('created_at')->where('absen_id', $this->id)->pluck('created_at') as $h => $a) {
            $hour = $a->format('H:i');

            foreach ($this::$settedTime as $i => $time) {
                if ($i == count($this::$settedTime) - 1) {
                    if ($hour >= $time && $hour < $this::$max) {
                        $aktivitas[$i] = $hour;
                    }
                    break;
                }
                if ($hour >= $time && $hour < $this::$settedTime[$i + 1]) {
                    $aktivitas[$i] = $hour;
                }
            }
            $aktivitas;
        };
        return $aktivitas;
    }
    public function getAktivitass()
    {
        foreach ($this::$settedTime as $i => $time) {
            $aktivitas[$i] = '';
        }
        foreach (Aktivitas::select('id', 'aktivitas', 'foto', 'alamat', 'koordinat', 'created_at')->where('absen_id', $this->id)->get() as $a) {
            $hour = $a->created_at->format('H:i');

            foreach ($this::$settedTime as $i => $time) {
                if ($i == count($this::$settedTime) - 1) {
                    if ($hour >= $time && $hour < $this::$max) {
                        $aktivitas[$i] = $a;
                        $a->time = $a->created_at->format('H:i');
                    }
                    break;
                }
                if ($hour >= $time && $hour < $this::$settedTime[$i + 1]) {
                    $aktivitas[$i] = $a;
                    $a->time = $a->created_at->format('H:i');
                }
            }
        };
        return $aktivitas;
    }
    public function getStatus()
    {
        switch ($this->status) {
            case 1:
                return 'Hadir';
            case 2:
                return 'Terlambat';
            case 3:
                return 'Alpa';
        }

    }
    // public function getStatus()
    // {

    //     $status = ['', 'bg-gradient-secondary'];
    //     switch ($this->status) {
    //         case 1:
    //             $status = ['Hadir', 'bg-gradient-success'];
    //             break;
    //         case 2:
    //             $status = ['Terlambat', 'bg-gradient-warning'];
    //             break;
    //         case 3:
    //             $status = ['Tidak hadir', 'bg-gradient-danger'];
    //             break;
    //     }


    //     if ($this->created_at->isSameDay(now())) {
    //         if (date('H:i') < ($this::$max)) {
    //             $status = ['Proses', 'bg-gradient-secondary'];
    //         }
    //     }

    //     return $status;
    // }
    public function isAbsen()
    {
        $last =  Aktivitas::where('absen_id', $this->id)->latest()->first()->created_at->format('H:i');
        $now = date('H:i');

        // cek apakah absen terakhir merupakan terakhir
        if ($last >= end($this::$settedTime)) {
            return false;
        }

        // cek apakah sekarang sudah masuk waktu absen
        if ($now >= $this::$settedTime[0] && $now < $this::$max) {
            for ($i = 0; $i < count($this::$settedTime) - 1; $i++) {
                if (($last >=  $this::$settedTime[$i]  && $last < $this::$settedTime[$i + 1]) && ($now >=  $this::$settedTime[$i]  && $now < $this::$settedTime[$i + 1])) {
                    return false;
                }
            }
        } else {
            return false;
        }

        return true;
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function aktivitass()
    {
        return $this->hasMany(Aktivitas::class);
    }
}
