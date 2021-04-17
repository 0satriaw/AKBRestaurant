<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Bahan;
use App\StokMasuk;
Use App\Menu;
use DB;

class StokMasukController extends Controller
{
  //Semua Stok Masuk tanpa terkecuali
    public function index(){
        $stokmasuk = StokMasuk::all();

        if(count($stokmasuk)>0){
            return response([
                'message'=>'Retrieve All Success',
                'data'=>$stokmasuk
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data'=>null
        ],404);
    }

// Show StokMasuk bahan tertentu//
    public function show($id_bahan){
        $stokmasuk = DB::table('stokmasuk')->where('id_bahan',$id_bahan)->get();

        if(!is_null($stokmasuk)){
            return response([
                'message'=>'Retrieve StokMasuk Success',
                'data'=>$stokmasuk
            ],200);
        }

        return response([
            'message'=>'StokMasuk Not Found',
            'data'=>null
        ],404);
    }

//Create StokMasuk baru cek
    public function store(Request $request){
        $storeData =$request->all();
        $id_bahan = $storeData['id_bahan'];
        // return $storeData;

        //-------------------------------------------Belum dicoba---------------------------------------------------------//
        $bahan = Bahan::where('id', $id_bahan)->first();
        $menu = Menu::where('id_bahan', $id_bahan)->first();

        $validate = Validator::make($storeData,[
            'id_bahan'=>'required|numeric',
            'jumlah'=>'required|numeric',
            'biaya'=>'required|numeric',
            'tanggal_masuk'=>'date|required'
        ]);

        $bahan['stok'] = $bahan['stok'] + $storeData['jumlah'];
        $menu['stok'] = $bahan['stok']/$menu['serving_size'];

        $bahan->save();
        $menu->save();

        if($validate->fails()){
            return response(['message'=>$validate->errors()],400);
        }

        $stokmasuk = StokMasuk::create($storeData);

        return response([
            'message'=>'Add StokMasuk Success',
            'data'=>$stokmasuk,
        ],200);
    }

    public function destroy($id){
        $stokmasuk = StokMasuk::find($id);
        $id_bahan = $stokmasuk['id_bahan'];

            if(is_null($stokmasuk)){
                return response([
                    'message' => 'Stok Masuk Not Found',
                    'data'=>null
                ],404);
            }

            $bahan = Bahan::where('id', $id_bahan)->first();
            $menu = Menu::where('id_bahan', $id_bahan)->first();

            $bahan['stok'] = $bahan['stok'] - $stokmasuk['jumlah'];
            $menu['stok'] = $bahan['stok']/$menu['serving_size'];

            $bahan->save();
            $menu->save();

            if($stokmasuk->delete()){
                return response([
                    'message' => 'Delete Stok Masuk Success',
                    'data' =>$stokmasuk,
                ],200);
            }
            return response([
                'message' => 'Delete Stok Masuk Failed',
                'data' => null,
            ],400);
    }

    //update status ketersediaan//
    public function update(Request $request, $id){
        $stokmasuk = StokMasuk::find($id);
        $sMasukAwal = $stokmasuk['jumlah'];
        $id_bahan = $stokmasuk['id_bahan'];

        if(is_null($stokmasuk)){
            return response([
                'message'=>'Stok Masuk Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_bahan'=>'required|numeric',
            'jumlah'=>'required|numeric',
            'biaya'=>'required|numeric',
            'tanggal_masuk'=>'date|required'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);


            $stokmasuk->id_bahan =  $updateData['id_bahan'];
            $stokmasuk->jumlah = $updateData['jumlah'];
            $stokmasuk->biaya = $updateData['biaya'];
            $stokmasuk->tanggal_masuk = $updateData['tanggal_masuk'];

            $bahan = Bahan::where('id', $id_bahan)->first();
            $menu = Menu::where('id_bahan', $id_bahan)->first();

            $bahan['stok'] = $bahan['stok'] + $updateData['jumlah'] - $sMasukAwal;
            $menu['stok'] = $bahan['stok']/$menu['serving_size'];

            $bahan->save();
            $menu->save();

        if($stokmasuk->save()){
            return response([
                'message' => 'Update Stok Masuk Success',
                'data'=> $stokmasuk,
            ],200);
        }

        return response([
            'messsage'=>'Update Stok Masuk Failed',
            'data'=>null,
        ],400);
    }
}
