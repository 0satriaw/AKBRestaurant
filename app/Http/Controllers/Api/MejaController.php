<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Meja;
use DB;

class MejaController extends Controller
{
    public function index(){
        $meja = DB::Table('mejas')->where('status_hapus','=',0)->get();

        if(count($meja)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$meja
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function show ($id){
        $meja=Meja::find($id);

        if(!is_null($meja)){
            return response([
                'message'  => 'Retrieve Meja Success',
                'data' => $meja
            ],200);

        }

        return response([
            'message' => 'Meja Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'status' => 'required|max:255',
            'nomor_meja' => 'required|numeric|unique:mejas',
            'status_hapus' => 'required|numeric'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $meja = Meja::create($storeData);
        return response([
            'message' => 'Data meja berhasil tambah',
            'data' => $meja,
        ],200);
    }

    public function destroy($id){
        $meja = Meja::find($id);

            if(is_null($meja)){
                return response([
                    'message' => 'Meja Not Found',
                    'data'=>null
                ],404);
            }

            if($meja->delete()){
                return response([
                    'message' => 'Data meja berhasil dihapus',
                    'data' =>$meja,
                ],200);
            }
            return response([
                'message' => 'Delete Meja Failed',
                'data' => null,
            ],400);
    }

    //update status ketersediaan//
    public function update(Request $request, $id){
        $meja = Meja::find($id);
        if(is_null($meja)){
            return response([
                'message'=>'Meja Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'nomor_meja' => ['numeric',Rule::unique('mejas')->ignore($meja)],
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

            $meja->nomor_meja = $updateData['nomor_meja'];

        if($meja->save()){
            return response([
                'message' => 'Data meja berhasil diubah',
                'data'=> $meja,
            ],200);
        }

        return response([
            'messsage'=>'Update Meja Failed',
            'data'=>null,
        ],400);
    }

    public function updateKetersediaan($updateData, $id){
        $meja = Meja::find($id);
        if(is_null($meja)){
            return response([
                'message'=>'Meja Not Found',
                'data'=>null
            ],404);
        }

        $validate = Validator::make($updateData,[
            'status' => 'required|string',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

            $meja->status = $updateData['status'];

        if($meja->save()){
            return response([
                'message' => 'Data meja berhasil diubah',
                'data'=> $meja,
            ],200);
        }

        return response([
            'messsage'=>'Update Meja Failed',
            'data'=>null,
        ],400);
    }

    public function sDestroy(Request $request, $id){
        $meja = Meja::find($id);
        if(is_null($meja)){
            return response([
                'message'=>'Meja Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'status_hapus' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

            $meja->status_hapus = $updateData['status_hapus'];

        if($meja->save()){
            return response([
                'message' => 'Data meja berhasil dihapus',
                'data'=> $meja,
            ],200);
        }

        return response([
            'messsage'=>'Update Meja Failed',
            'data'=>null,
        ],400);
    }
}
