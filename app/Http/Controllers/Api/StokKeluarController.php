<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Bahan;
use App\StokKeluar;
Use App\Menu;
use DB;

class StokKeluarController extends Controller
{
    //Semua Stok Keluar tanpa terkecuali
    public function index(){
        $stokkeluar = DB::table('stok_keluars')
        ->join('bahans', 'bahans.id', '=', 'stok_keluars.id_bahan')
        ->select('stok_keluars.*','bahans.nama_bahan')
        ->get();

        if(count($stokkeluar)>0){
            return response([
                'message'=>'Retrieve All Success',
                'data'=>$stokkeluar
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data'=>null
        ],404);
    }

    // Show StokKeluar bahan tertentu//
    public function show($id_bahan){
        $stokkeluar = DB::table('stokkeluar')->where('id_bahan',$id_bahan)->get();

        if(!is_null($stokkeluar)){
            return response([
                'message'=>'Retrieve StokKeluar Success',
                'data'=>$stokkeluar
            ],200);
        }

        return response([
            'message'=>'StokKeluar Not Found',
            'data'=>null
        ],404);
    }

    //Create StokKeluar baru cek
    public function store(Request $request){
        $storeData =$request->all();
        $id_bahan = $storeData['id_bahan'];
        // return $storeData;

        //-------------------------------------------Belum dicoba---------------------------------------------------------//
        $bahan = Bahan::where('id', $id_bahan)->first();
        $menu = Menu::where('id_bahan', $id_bahan)->first();

        if($bahan != null){
            if($bahan['stok']<$storeData['jumlah']){
                return response([
                    'message'=>'Jumlah pengurangan lebih dari stok',
                    'data'=>null,
                ],200);
            }
        }

         $validate = Validator::make($storeData,[
            'id_bahan'=>'required|numeric',
            'jumlah'=>'required|numeric',
            'tanggal_keluar'=>'date|required',
            'status'=>'required|numeric'
        ]);

        $bahan['stok'] = $bahan['stok'] - $storeData['jumlah'];
        $bahan->save();
        if($menu!=null){
            $menu['stok'] = $bahan['stok']/$menu['serving_size'];
            $menu->save();
        }


        if($validate->fails()){
            return response(['message'=>$validate->errors()],400);
        }

        $stokkeluar = StokKeluar::create($storeData);

        return response([
            'message'=>'Tambah Data Stok Keluar sukses',
            'data'=>$stokkeluar,
        ],200);
    }
}
