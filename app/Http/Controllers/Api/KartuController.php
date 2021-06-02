<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Kartu;

class KartuController extends Controller
{
    public function indexC(){
        $kartu = Kartu::where('tipe_kartu',1)->get();

        if(count($kartu)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$kartu
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function indexD(){
        $kartu = Kartu::where('tipe_kartu',2)->get();

        if(count($kartu)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$kartu
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function show ($id){
        $kartu=Kartu::find($id);

        if(!is_null($kartu)){
            return response([
                'message'  => 'Retrieve Kartu Success',
                'data' => $kartu
            ],200);

        }

        return response([
            'message' => 'Kartu Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();


        $validate = Validator::make($storeData,[
            'tipe_kartu' => 'required|numeric',
            'nomor_kartu' => 'required|numeric',
            'nama_pemilik' => 'string|nullable',
            'exp_date' => 'nullable|date_format:Y-m-d'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        $kartu = Kartu::create($storeData);
        return response([
            'message' => 'Add Kartu Success',
            'data' => $kartu,
        ],200);
    }

    public function destroy($id){
        $kartu = Kartu::find($id);

            if(is_null($kartu)){
                return response([
                    'message' => 'Kartu Not Found',
                    'data'=>null
                ],404);
            }

            if($kartu->delete()){
                return response([
                    'message' => 'Delete Kartu Success',
                    'data' =>$kartu,
                ],200);
            }
            return response([
                'message' => 'Delete Kartu Failed',
                'data' => null,
            ],400);
    }

    public function update(Request $request, $id){
        $kartu = Kartu::find($id);
        if(is_null($kartu)){
            return response([
                'message'=>'Kartu Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'tipe_kartu' => 'required|max:255',
            'nomor_kartu' => 'required|numeric',
            'nama_pemilik' => 'string|nullable',
            'exp_date' => 'nullable|'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


            $kartu->tipe_kartu =  $updateData['tipe_kartu'];
            $kartu->nomor_kartu = $updateData['nomor_kartu'];
            $kartu->nama_pemilik = $updateData['nama_pemilik'];
            $kartu->exp_date = $updateData['exp_date'];

        if($kartu->save()){
            return response([
                'message' => 'Update Kartu Success',
                'data'=> $kartu,
            ],200);
        }

        return response([
            'messsage'=>'Update Kartu Failed',
            'data'=>null,
        ],400);
    }

}
