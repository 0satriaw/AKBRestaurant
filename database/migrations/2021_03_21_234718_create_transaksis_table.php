<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_reservasi');
            $table->foreign('id_reservasi')->references('id')->on('reservasis');

            $table->unsignedBigInteger('id_kartu');
            $table->foreign('id_kartu')->references('id')->on('kartus');

            $table->unsignedBigInteger('id_pegawai');
            $table->foreign('id_pegawai')->references('id')->on('users');

            $table->double('total_transaksi');
            $table->string('metode_pembayaran');
            $table->date('tanggal_transaksi');
            $table->string('kode_transaksi');
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
        Schema::dropIfExists('transaksis');
    }
}
