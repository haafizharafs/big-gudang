<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aktivitas extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public function absen()
    {
        return $this->belongsTo(Absen::class);

    }
    public function user()
    {
        return $this->belongsTo(User::class);

    }
}
