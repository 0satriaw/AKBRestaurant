<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Menu;
use App\Bahan;

class MenuController extends Controller
{
    // =======================================MENU BELUM ADA ID BAHAN============================================
    public function index(){
        $menu = Menu::all()->where('status_hapus','=',0);

        if(count($menu)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$menu
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);


    }

    public function show ($id){
        $menu=Menu::find($id);


        if(!is_null($menu)){
            return response([
                'message'  => 'Retrieve Menu Success',
                'data' => $menu
            ],200);

        }

        return response([
            'message' => 'Menu Not Found',
            'data' => null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData,[
            'nama_menu' => 'required|max:60|unique:menus',
            'id_bahan'=>'required|numeric',
            'deskripsi' => 'required|max:255',
            'unit' => 'required|max:255',
            'tipe' => 'required|max:255',
            'stok' => 'required|numeric',
            'harga' => 'required|numeric',
            'serving_size' => 'required|numeric',
            'status_hapus' => 'required|numeric'
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

            //tambahan image biar bisa langsung pas add
        if($files = $request->file('gambar')){
        $imageName = $files->getClientOriginalName();
        $request->gambar->move(public_path('images'),$imageName);

        $menu = Menu::create([
            'id_bahan'=>$request->id_bahan,
            'nama_menu'=>$request->nama_menu,
            'deskripsi'=>$request->deskripsi,
            'unit'=>$request->unit,
            'tipe'=>$request->tipe,
            'stok'=>$request->stok,
            'harga'=>$request->harga,
            'serving_size'=>$request->serving_size,
            'status_hapus'=>$request->status_hapus,
            'gambar'=>'/images/'.$imageName,
        ]);
        return response([
            'message'=>'Add Menu Success',
            'data'=>$menu
        ],200);
        } else{
            return response([
                'message' => 'Gambar Menu tidak boleh kosong',
                'data' => null,
            ],400);
        }
    }

    public function destroy($id){
        $menu = Menu::find($id);

            if(is_null($menu)){
                return response([
                    'message' => 'Menu Not Found',
                    'data'=>null
                ],404);
            }

            if($menu->delete()){
                return response([
                    'message' => 'Delete Menu Success',
                    'data' =>$menu,
                ],200);
            }
            return response([
                'message' => 'Delete Menu Failed',
                'data' => null,
            ],400);

    }

    //update tanpa gambar
    public function update(Request $request, $id){
        $menu = Menu::find($id);
        if(is_null($menu)){
            return response([
                'message'=>'Menu Not Found',
                'data'=>null
            ],404);
        }


        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_bahan'=>'numeric|numeric',
            'nama_menu' => ['max:255',Rule::unique('menus')->ignore($menu)],
            'deskripsi' => 'required|max:255',
            'unit' => 'required|max:255',
            'tipe' => 'required|max:255',
            'stok' => 'required|numeric',
            'harga' => 'required|numeric',
            'serving_size' => 'required|numeric',
            'status_hapus' => 'required|numeric'
        ]);


        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

            $menu->nama_menu =  $updateData['nama_menu'];
            $menu->deskripsi = $updateData['deskripsi'];
            $menu->unit= $updateData['unit'];
            $menu->tipe = $updateData['tipe'];
            $menu->stok = $updateData['stok'];
            $menu->harga = $updateData['harga'];
            $menu->serving_size = $updateData['serving_size'];
            $menu->status_hapus = $updateData['status_hapus'];


        if($menu->save()){
            return response([
                'message' => 'Update Menu Success',
                'data'=> $menu,
            ],200);
        }

        return response([
            'messsage'=>'Update Menu Failed',
            'data'=>null,
        ],400);
    }

    public function uploadGambar(Request $request, $id){
        $menu= Menu::find($id);
        if(is_null($menu)){
            return response([
                'message' => 'Menu not found',
                'data' => null
            ],404);
        }

        if(!$request->hasFile('gambar')) {
            return response([
                'message' => 'Upload Photo Menu Failed',
                'data' => null,
            ],400);
        }
        $file = $request->file('gambar');

        if(!$file->isValid()) {
            return response([
                'message'=> 'Upload Photo Menu Failed',
                'data'=> null,
            ],400);
        }

        $image = public_path().'/images/';
        $file -> move($image, $file->getClientOriginalName());
        $image = '/images/'.$file->getClientOriginalName();
        $updateData = $request->all();
        Validator::make($updateData, [
            'gambar' => $image
        ]);
        $menu->gambar = $image;


        if($menu->save()){
            return response([
                'message' => 'Upload Photo Menu Success',
                'path' => $image,
            ],200);
        }

        return response([
            'messsage'=>'Upload Photo Menu Failed',
            'data'=>null,
        ],400);

    }

    public function sDestroy(Request $request, $id){
        $menu = Menu::find($id);
        if(is_null($menu)){
            return response([
                'message'=>'Menu Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'status_hapus' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

            $menu->status_hapus = $updateData['status_hapus'];

        if($menu->save()){
            return response([
                'message' => 'Delete Menu Success',
                'data'=> $menu,
            ],200);
        }

        return response([
            'messsage'=>'Update Menu Failed',
            'data'=>null,
        ],400);
    }
}
