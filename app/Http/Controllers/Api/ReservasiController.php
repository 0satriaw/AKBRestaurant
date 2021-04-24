<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Reservasi;
use App\Meja;
use App\Pelanggan;
use App\User;
use DB;

class ReservasiController extends Controller
{
    public function index(){
        $reservasi = DB::table('reservasis')
        ->join('mejas', 'reservasis.id_meja', '=', 'mejas.id')
        ->join('pelanggans','reservasis.id_pelanggan','=','pelanggans.id')
        ->join('users','reservasis.id_pegawai','=','users.id')
        ->select('users.nama', 'mejas.nomor_meja','pelanggans.nama_pelanggan','reservasis.*')->where('reservasis.status_hapus','=',0)
        ->get();

        if(count($reservasi)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$reservasi
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function show ($id){
        $reservasi=Reservasi::find($id);

        if(!is_null($reservasi)){
            return response([
                'message'  => 'Retrieve Reservasi Success',
                'data' => $reservasi
            ],200);

        }

        return response([
            'message' => 'Reservasi Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();


        $validate = Validator::make($storeData,[
            'id_pelanggan' => 'required|numeric',
            'id_meja' => 'required|numeric',
            'id_pegawai' => 'required|numeric',
            'kode_qr'=>'required',
            'tanggal_kunjungan'=>'required|date_format:Y-m-d',
            'jam_kunjungan'=>'required|date_format:H:i:s',
            'sesi'=>'required|numeric'
        ]);



        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


        $req = array(
                'status' => 'Tidak Tersedia',
              );
        if($storeData['sesi']==0){
            app(MejaController::class)->updateKetersediaan($req,$storeData['id_meja']);
        }

        $reservasi = Reservasi::create($storeData);
        return response([
            'message' => 'Add Reservasi Success',
            'data' => $reservasi,
        ],200);
    }

    public function destroy($id){
        $reservasi = Reservasi::find($id);

            if(is_null($reservasi)){
                return response([
                    'message' => 'Reservasi Not Found',
                    'data'=>null
                ],404);
            }

            if($reservasi->delete()){
                return response([
                    'message' => 'Delete Reservasi Success',
                    'data' =>$reservasi,
                ],200);
            }
            return response([
                'message' => 'Delete Reservasi Failed',
                'data' => null,
            ],400);
    }

    //update status ketersediaan//
    public function update(Request $request, $id){
        $reservasi = Reservasi::find($id);
        if(is_null($reservasi)){
            return response([
                'message'=>'Reservasi Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_meja' => 'required|numeric',
            'id_pegawai' => 'required|numeric',
            'kode_qr'=>'required',
            'tanggal_kunjungan'=>'required|date_format:Y-m-d',
            'jam_kunjungan'=>'required|date_format:H:i:s',
            'sesi'=>'required|numeric'
        ]);


        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


            if($updateData['sesi']!=0&&$reservasi->sesi==0){
                $req = array(
                    'status' => 'Tersedia',
                );
                app(MejaController::class)->updateKetersediaan($req,$reservasi->id_meja);
            }
            if($updateData['sesi']==0&&$reservasi->sesi!=0){
                $req = array(
                    'status' => 'Tidak Tersedia',
                );
                app(MejaController::class)->updateKetersediaan($req,$reservasi->id_meja);
            }
            $reservasi->id_meja =  $updateData['id_meja'];
            $reservasi->id_pegawai = $updateData['id_pegawai'];
            $reservasi->kode_qr = $updateData['kode_qr'];
            $reservasi->tanggal_kunjungan = $updateData['tanggal_kunjungan'];
            $reservasi->jam_kunjungan = $updateData['jam_kunjungan'];
            $reservasi->sesi = $updateData['sesi'];



        if($reservasi->save()){
            return response([
                'message' => 'Update Reservasi Success',
                'data'=> $reservasi,
            ],200);
        }

        return response([
            'messsage'=>'Update Reservasi Failed',
            'data'=>null,
        ],400);
    }

    public function sDestroy(Request $request, $id){
        $reservasi = Reservasi::find($id);
        if(is_null($reservasi)){
            return response([
                'message'=>'Reservasi Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'status_hapus' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

            $reservasi->status_hapus = $updateData['status_hapus'];

        $req = array(
                'status' => 'Tersedia',
              );
        if($reservasi['sesi']==0){
            app(MejaController::class)->updateKetersediaan($req,$reservasi['id_meja']);
        }

        if($reservasi->save()){
            return response([
                'message' => 'Delete Reservasi Success',
                'data'=> $reservasi,
            ],200);
        }

        return response([
            'messsage'=>'Update Reservasi Failed',
            'data'=>null,
        ],400);
    }
}
