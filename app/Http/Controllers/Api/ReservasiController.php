<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Reservasi;
use App\Meja;

class ReservasiController extends Controller
{
    public function index(){
        $reservasi = Reservasi::all();

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

    //store reservasi belom samsek!!!
    public function store(Request $request){
        $storeData = $request->all();
        $id_meja = $storeData['id_meja'];
        $todayDate = date('Y-m-d');

        // return

        // if($todayDate==$storeData['tanggal_kunjungan']){
        //     $meja = Bahan::where(['id', $id_meja],
        //     ['status','=','Tersedia'])->first();
        // }else{
        //     $reservasi =
        // }


        $validate = Validator::make($storeData,[
            'id_pelanggan' => 'required|numeric',
            'id_meja' => 'required|numeric',
            'id_pegawai' => 'required|numeric',
            'kode_qr'=>'required',
            'tanggal_kunjungan'=>'required|date_format:Y-m-d|after_or_equal:'.$todayDate,
            'jam_kunjungan'=>'required|date_format:G:i',
            'sesi'=>'required|numeric'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

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
            'status' => 'required|max:255',
            'nomor_meja' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


            $reservasi->status =  $updateData['status'];
            $reservasi->nomor_meja = $updateData['nomor_meja'];

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
