<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesanansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_reservasi');
            $table->foreign('id_reservasi')->references('id')->on('reservasis');

            $table->unsignedBigInteger('id_transaksi');
            $table->foreign('id_transaksi')->references('id')->on('transaksis');

            $table->unsignedBigInteger('id_menu');
            $table->foreign('id_menu')->references('id')->on('menus');

            $table->integer('jumlah');
            $table->double('total_harga');
            $table->integer('status_pesanan');
            $table->datetime('tanggal_pesanan');
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
        Schema::dropIfExists('pesanans');
    }
}
