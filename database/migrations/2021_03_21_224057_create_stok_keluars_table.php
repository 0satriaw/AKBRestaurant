<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStokKeluarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stok_keluars', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_bahan');
            $table->foreign('id_bahan')->references('id')->on('bahans');
            $table->integer('status');

            $table->integer('jumlah');
            $table->date('tanggal_keluar');
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
        Schema::dropIfExists('stok_keluars');
    }
}
