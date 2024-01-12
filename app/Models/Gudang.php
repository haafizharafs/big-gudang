<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gudang extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function barang(){
        return $this->hasMany(Barang::Class);
    }
    public function mutasiMasuk(){
        return $this->belongsTo(MutasiMasuk::Class);
    }
    public function mutasiKeluar(){
        return $this->belongsTo(MutasiKeluar::Class);
    }
    public function mutasiKembali(){
        return $this->belongsTo(MutasiKembali::Class);
    }
}
