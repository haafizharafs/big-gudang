<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function gudang(){
        return $this->BelongsTo(Gudang::Class);
    }
    public function kategori(){
        return $this->BelongsTo(Kategori::Class);
    }
    public function satuan(){
        return $this->BelongsTo(Satuan::Class);
    }
    public function mutasiMasuk(){
        return $this->BelongsTo(mutasiMasuk::Class);
    }
    public function mutasiKeluar(){
        return $this->BelongsTo(mutasiKeluar::Class);
    }
    public function mutasiKembali(){
        return $this->BelongsTo(mutasiKembali::Class);
    }
}
