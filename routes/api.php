<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login','Api\PegawaiController@login');

//coba

Route::group(['middleware'=>'auth:api'],function(){

    //USER
    Route::get('user','Api\PegawaiController@index');
    Route::get('user/{id}','Api\PegawaiController@show');
    Route::post('user','Api\PegawaiController@register');
    Route::put('user/{id}','Api\PegawaiController@update');
    Route::put('duser/{id}','Api\PegawaiController@sDestroy');
    Route::put('updatepassword/{id}','Api\PegawaiController@updatePassword');
    Route::delete('user/{id}','Api\PegawaiController@destroy');
    Route::post('logout','Api\PegawaiController@logout');

    //Bahan
    Route::get('bahan','Api\BahanController@index');
    Route::get('bahan/{id}','Api\BahanController@show');
    Route::post('bahan','Api\BahanController@store');
    Route::put('bahan/{id}','Api\BahanController@update');
    Route::put('dbahan/{id}','Api\BahanController@sDestroy');
    Route::delete('bahan/{id}','Api\BahanController@destroy');

    //Jabatan
    Route::get('jabatan','Api\JabatanController@index');
    Route::get('jabatan/{id}','Api\JabatanController@show');
    Route::post('jabatan','Api\JabatanController@store');
    Route::put('jabatan/{id}','Api\JabatanController@update');
    Route::delete('jabatan/{id}','Api\JabatanController@destroy');

    //Kartu
    Route::get('kartu','Api\KartuController@index');
    Route::get('kartu/{id}','Api\KartuController@show');
    Route::post('kartu','Api\KartuController@store');
    Route::put('kartu/{id}','Api\KartuController@update');
    Route::delete('kartu/{id}','Api\KartuController@destroy');

    //Meja
    Route::get('meja','Api\MejaController@index');
    Route::get('meja/{id}','Api\MejaController@show');
    Route::post('meja','Api\MejaController@store');
    Route::put('meja/{id}','Api\MejaController@update');
    Route::put('dmeja/{id}','Api\MejaController@sDestroy');
    Route::delete('meja/{id}','Api\MejaController@destroy');

    //Pelanggan
    Route::get('pelanggan','Api\PelangganController@index');
    Route::get('pelanggan/{id}','Api\PelangganController@show');
    Route::post('pelanggan','Api\PelangganController@store');
    Route::put('pelanggan/{id}','Api\PelangganController@update');
    Route::put('dpelanggan/{id}','Api\PelangganController@sDestroy');
    Route::delete('pelanggan/{id}','Api\PelangganController@destroy');

    //StokKeluar
    Route::get('stokkeluar','Api\StokKeluarController@index');
    Route::get('stokkeluar/{id}','Api\StokKeluarController@show');
    Route::post('stokkeluar','Api\StokKeluarController@store');

    //Stok Masuk
    Route::get('stokmasuk','Api\StokMasukController@index');
    Route::get('stokmasuk/{id}','Api\StokMasukController@show');
    Route::post('stokmasuk','Api\StokMasukController@store');
    Route::put('stokmasuk/{id}','Api\StokMasukController@update');
    Route::delete('stokmasuk/{id}','Api\StokMasukController@destroy');

    //Menu
    Route::get('menu','Api\MenuController@index');
    Route::get('menu/{id}','Api\MenuController@show');
    Route::post('menu','Api\MenuController@store');
    Route::put('menu/{id}','Api\MenuController@update');
    Route::put('dmenu/{id}','Api\MenuController@sDestroy');
    Route::delete('menu/{id}','Api\MenuController@destroy');
    Route::post('menu/gambar/{id}', 'Api\MenuController@uploadGambar');

    Route::get('reservasi','Api\ReservasiController@index');
    Route::get('reservasi/{id}','Api\ReservasiController@show');
    Route::post('reservasi','Api\ReservasiController@store');
    Route::put('reservasi/{id}','Api\ReservasiController@update');
    Route::put('dreservasi/{id}','Api\ReservasiController@sDestroy');
    Route::delete('reservasi/{id}','Api\ReservasiController@destroy');

});
