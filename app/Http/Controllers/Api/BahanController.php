<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Bahan;
use DB;

class BahanController extends Controller
{
    public function index(){
        $bahan = DB::Table('bahans')->where('status_hapus','=',0)->get();

        if(count($bahan)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$bahan
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function show ($id){
        $bahan=Bahan::find($id);

        if(!is_null($bahan)){
            return response([
                'message'  => 'Retrieve Bahan Success',
                'data' => $bahan
            ],200);

        }

        return response([
            'message' => 'Bahan Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'nama_bahan' => 'required|max:255',
            'stok' => 'required|numeric',
            'satuan' => 'required',
            'tipe_bahan' => 'required',
            'status_hapus' => 'required|numeric'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $bahan = Bahan::create($storeData);
        return response([
            'message' => 'Add Bahan Success',
            'data' => $bahan,
        ],200);
    }

    public function destroy($id){
        $bahan = Bahan::find($id);

            if(is_null($bahan)){
                return response([
                    'message' => 'Bahan Not Found',
                    'data'=>null
                ],404);
            }

            if($bahan->delete()){
                return response([
                    'message' => 'Delete Bahan Success',
                    'data' =>$bahan,
                ],200);
            }
            return response([
                'message' => 'Delete Bahan Failed',
                'data' => null,
            ],400);
    }

    public function update(Request $request, $id){
        $bahan = Bahan::find($id);
        if(is_null($bahan)){
            return response([
                'message'=>'Bahan Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'nama_bahan' => 'required|max:255',
            'stok' => 'required|numeric',
            'satuan' => 'required',
            'tipe_bahan' => 'required',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


            $bahan->nama_bahan =  $updateData['nama_bahan'];
            $bahan->stok = $updateData['stok'];
            $bahan->satuan = $updateData['satuan'];
            $bahan->tipe_bahan = $updateData['tipe_bahan'];

        if($bahan->save()){
            return response([
                'message' => 'Update Bahan Success',
                'data'=> $bahan,
            ],200);
        }

        return response([
            'messsage'=>'Update Bahan Failed',
            'data'=>null,
        ],400);
    }

    public function sDestroy(Request $request, $id){
        $bahan = Bahan::find($id);
        if(is_null($bahan)){
            return response([
                'message'=>'Bahan Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'status_hapus' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

            $bahan->status_hapus = $updateData['status_hapus'];

        if($bahan->save()){
            return response([
                'message' => 'Delete Bahan Success',
                'data'=> $bahan,
            ],200);
        }

        return response([
            'messsage'=>'Delete Bahan Failed',
            'data'=>null,
        ],400);
    }
}
