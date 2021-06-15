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
//Reservasi
Route::get('scanqr/{req}','Api\ReservasiController@scanQR');
//Menu
Route::get('menu','Api\MenuController@index');
//Pesanan
Route::get('showorder/{id}','Api\PesananController@showOrder');
Route::get('showpesanan/{id}','Api\PesananController@showPesanan');
Route::get('pesanan/{id}','Api\PesananController@show');
Route::put('upesanan/{id}','Api\PesananController@updateStatus');
Route::post('pesanan','Api\PesananController@store');
Route::put('updatecart/{id}','Api\PesananController@updateCart');
Route::put('updatepesanan/{id}','Api\PesananController@updatePesanan');
route::put('endreservasi/{id}','Api\ReservasiController@endReservasi');
Route::delete('pesanan/{id}','Api\PesananController@destroy');
Route::put('pesanan/{id}','Api\PesananController@update');
Route::put('dpesanan/{id}','Api\PesananController@sDestroy');

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
    Route::get('kartuc','Api\KartuController@indexC');
    Route::get('kartud','Api\KartuController@indexD');
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
    Route::put('dstokmasuk/{id}','Api\StokMasukController@sDestroy');
    Route::delete('stokmasuk/{id}','Api\StokMasukController@destroy');

    //Menu

    Route::get('menu/{id}','Api\MenuController@show');
    Route::post('menu','Api\MenuController@store');
    Route::put('menu/{id}','Api\MenuController@update');
    Route::put('dmenu/{id}','Api\MenuController@sDestroy');
    Route::delete('menu/{id}','Api\MenuController@destroy');
    Route::post('menu/gambar/{id}', 'Api\MenuController@uploadGambar');

    //Reservasi
    Route::get('reservasi','Api\ReservasiController@index');
    Route::get('reservasi/{id}','Api\ReservasiController@show');
    Route::post('reservasi','Api\ReservasiController@store');
    Route::put('reservasi/{id}','Api\ReservasiController@update');
    Route::put('dreservasi/{id}','Api\ReservasiController@sDestroy');
    Route::delete('reservasi/{id}','Api\ReservasiController@destroy');

    //Pesanan
    Route::get('pesanan','Api\PesananController@index');




    //Transaksi
    Route::get('transaksi/{id}','Api\TransaksiController@show');
    Route::get('transaksi','Api\TransaksiController@index');
    Route::put('transaksi/{id}','Api\TransaksiController@update');
    Route::get('transaksil','Api\TransaksiController@indexlunas');

    ///MULAI DARI SINI COPAS KE API CODE 3
    Route::get('gettahun','Api\TransaksiController@getTahun');
    Route::get('getnamamenu','Api\TransaksiController@getNamaMenu');
    Route::get('gettahunkeluar','Api\TransaksiController@getTahunKeluar');
    Route::get('cetakstruk/{id}','Api\TransaksiController@cetakStruk');
    Route::get('pendperbulan/{tahun}','Api\TransaksiController@laporanPendapatanPerbulan');
    Route::get('pendpertahun/{tahun1}/{tahun2}','Api\TransaksiController@laporanPendapatanPertahun');
    Route::get('pengperbulan/{tahun}','Api\TransaksiController@laporanPengeluaranPerbulan');
    Route::get('pengpertahun/{tahun1}/{tahun2}','Api\TransaksiController@laporanPengeluaranPertahun');
    Route::get('lappenjualan/{dt}','Api\TransaksiController@laporanPenjualan');
    Route::get('lappenjualanpertahun/{dt}','Api\TransaksiController@laporanPenjualanPertahun');
    Route::get('laporanstok/{tgl1}/{tgl2}','Api\TransaksiController@laporanStok');
    Route::get('laporanstokperbulan/{tgl1}/{nama_menu}','Api\TransaksiController@laporanStokPerbulan');
});
