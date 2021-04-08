<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Jabatan;

class JabatanController extends Controller
{
    public function index(){
        $jabatan = Jabatan::all();

        if(count($jabatan)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$jabatan
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function show ($id){
        $jabatan=Jabatan::find($id);

        if(!is_null($jabatan)){
            return response([
                'message'  => 'Retrieve Jabatan Success',
                'data' => $jabatan
            ],200);

        }

        return response([
            'message' => 'Jabatan Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'nama_jabatan' => 'required|max:255',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $jabatan = Jabatan::create($storeData);
        return response([
            'message' => 'Add Jabatan Success',
            'data' => $jabatan,
        ],200);
    }

    public function destroy($id){
        $jabatan = Jabatan::find($id);

            if(is_null($jabatan)){
                return response([
                    'message' => 'Jabatan Not Found',
                    'data'=>null
                ],404);
            }

            if($jabatan->delete()){
                return response([
                    'message' => 'Delete Jabatan Success',
                    'data' =>$jabatan,
                ],200);
            }
            return response([
                'message' => 'Delete Jabatan Failed',
                'data' => null,
            ],400);
    }

    //update status//
    public function update(Request $request, $id){
        $jabatan = Jabatan::find($id);
        if(is_null($jabatan)){
            return response([
                'message'=>'Jabatan Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'nama_jabatan' => 'required|max:255',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


            $jabatan->nama_jabatan =  $updateData['nama_jabatan'];

        if($jabatan->save()){
            return response([
                'message' => 'Update Jabatan Success',
                'data'=> $jabatan,
            ],200);
        }

        return response([
            'messsage'=>'Update Jabatan Failed',
            'data'=>null,
        ],400);
    }

}
