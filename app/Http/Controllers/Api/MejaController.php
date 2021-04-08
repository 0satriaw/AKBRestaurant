<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Meja;

class MejaController extends Controller
{
    public function index(){
        $meja = Meja::all();

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
            'nomor_meja' => 'required|numeric',
            'status_hapus' => 'required|numeric'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $meja = Meja::create($storeData);
        return response([
            'message' => 'Add Meja Success',
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
                    'message' => 'Delete Meja Success',
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
            'status' => 'required|max:255',
            'nomor_meja' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


            $meja->status =  $updateData['status'];
            $meja->nomor_meja = $updateData['status_hapus'];

        if($meja->save()){
            return response([
                'message' => 'Update Meja Success',
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
                'message' => 'Delete Meja Success',
                'data'=> $meja,
            ],200);
        }

        return response([
            'messsage'=>'Update Meja Failed',
            'data'=>null,
        ],400);
    }
}
