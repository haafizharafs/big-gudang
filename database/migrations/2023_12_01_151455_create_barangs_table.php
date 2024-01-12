<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->foreignId('kategori_id')->references('id')->on('kategoris')->onDelete('cascade');
            $table->string('serial_number')->nullable();
            $table->string('nama');
            $table->foreignId('gudang_id')->references('id')->on('gudangs')->onDelete('cascade');
            $table->foreignId('satuan_id')->references('id')->on('satuans')->onDelete('cascade');
            $table->integer('jumlah');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barangs');
    }
};
