<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class TransaksiController extends Controller
{
    public function index(){
        $transaksi = DB::Table('transaksis')
        ->join('reservasis','reservasis.id','transaksis.id_reservasi')
        ->join('mejas','mejas.id','reservasis.id_meja')
        ->join('users','users.id','reservasis.id_pegawai')
        ->join('pelanggans','pelanggans.id','reservasis.id_pelanggan')
        ->select('transaksis.*','mejas.nomor_meja','users.nama','pelanggans.nama_pelanggan')->where('status_transaksi','=',0)
        ->get();

        if(count($transaksi)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$transaksi
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function indexlunas(){
        $transaksi = DB::Table('transaksis')
        ->join('reservasis','reservasis.id','transaksis.id_reservasi')
        ->join('mejas','mejas.id','reservasis.id_meja')
        ->join('users','users.id','reservasis.id_pegawai')
        ->join('pelanggans','pelanggans.id','reservasis.id_pelanggan')
        ->select('transaksis.*','mejas.nomor_meja','users.nama','pelanggans.nama_pelanggan')->where('status_transaksi','=',1)
        ->get();

        if(count($transaksi)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$transaksi
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function show ($id){
        $transaksi= Transaksi::find($id);

        if(!is_null($transaksi)){
            return response([
                'message'  => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ],200);

        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ],404);
    }

    public function update(Request $request, $id){
        $transaksi = Transaksi::find($id);
        if(is_null($transaksi)){
            return response([
                'message'=>'Transaksi Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_kartu' => 'nullable|numeric',
            'metode_pembayaran' => 'required',
            'status_transaksi'=>'numeric'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

            $transaksi->id_kartu =  $updateData['id_kartu'];
            $transaksi->metode_pembayaran = $updateData['metode_pembayaran'];
            $transaksi->status_transaksi = $updateData['status_transaksi'];

        if($transaksi->save()){
            return response([
                'message' => 'Data Transaksi berhasil diubah',
                'data'=> $transaksi,
            ],200);
        }

        return response([
            'messsage'=>'Update Transaksi Failed',
            'data'=>null,
        ],400);
    }

}
