<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Pelanggan;

class PelangganController extends Controller
{
    public function index(){
        $pelanggan = Pelanggan::all();

        if(count($pelanggan)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$pelanggan
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function show ($id){
        $pelanggan=Pelanggan::find($id);

        if(!is_null($pelanggan)){
            return response([
                'message'  => 'Retrieve Pelanggan Success',
                'data' => $pelanggan
            ],200);

        }

        return response([
            'message' => 'Pelanggan Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'nama_pelanggan' => 'required|max:255',
            'no_telp'=>'nullable|numeric|digits_between:10,13|starts_with:08',
            'email'=>'nullable|email:rfc,dns|unique:users',
            'status_hapus' => 'required|numeric'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $pelanggan = Pelanggan::create($storeData);
        return response([
            'message' => 'Add Pelanggan Success',
            'data' => $pelanggan,
        ],200);
    }

    public function destroy($id){
        $pelanggan = Pelanggan::find($id);

            if(is_null($pelanggan)){
                return response([
                    'message' => 'Pelanggan Not Found',
                    'data'=>null
                ],404);
            }

            if($pelanggan->delete()){
                return response([
                    'message' => 'Delete Pelanggan Success',
                    'data' =>$pelanggan,
                ],200);
            }
            return response([
                'message' => 'Delete Pelanggan Failed',
                'data' => null,
            ],400);
    }

    public function update(Request $request, $id){
        $pelanggan = Pelanggan::find($id);
        if(is_null($pelanggan)){
            return response([
                'message'=>'Pelanggan Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'nama_pelanggan' => 'required|max:255',
            'no_telp'=>'nullable|numeric|digits_between:10,13|starts_with:08',
            'email'=>'nullable|email:rfc,dns|unique:users',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


            $pelanggan->nama_pelanggan =  $updateData['nama_pelanggan'];
            $pelanggan->no_telp = $updateData['no_telp'];
            $pelanggan->email = $updateData['email'];

        if($pelanggan->save()){
            return response([
                'message' => 'Update Pelanggan Success',
                'data'=> $pelanggan,
            ],200);
        }

        return response([
            'messsage'=>'Update Pelanggan Failed',
            'data'=>null,
        ],400);
    }

    public function sDestroy(Request $request, $id){
        $pelanggan = Pelanggan::find($id);
        if(is_null($pelanggan)){
            return response([
                'message'=>'Pelanggan Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'status_hapus' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

            $pelanggan->status_hapus = $updateData['status_hapus'];

        if($pelanggan->save()){
            return response([
                'message' => 'Delete Pelanggan Success',
                'data'=> $pelanggan,
            ],200);
        }

        return response([
            'messsage'=>'Delete Pelanggan Failed',
            'data'=>null,
        ],400);
    }
}
