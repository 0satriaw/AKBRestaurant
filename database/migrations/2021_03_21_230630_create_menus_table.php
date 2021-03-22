<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_bahan');
            $table->foreign('id_bahan')->references('id')->on('bahans');

            $table->string('nama_menu');
            $table->string('deskripsi');
            $table->string('unit');
            $table->string('tipe');
            $table->integer('stok');
            $table->double('harga');
            $table->integer('serving_size');
            $table->string('gambar');
            $table->integer('status_hapus');
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
        Schema::dropIfExists('menus');
    }
}
