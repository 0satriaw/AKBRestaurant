<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Reservasi;
use App\Pelanggan;
use App\Pesanan;
use App\Menu;
use App\User;
use DB;

class PesananController extends Controller
{
  //Semua Orderan Orang tanpa terkecuali
    public function index(){
        $pesanans = DB::table('pesanans')
        ->join('reservasis', 'reservasis.id', '=', 'pesanans.id_reservasi')
        ->join('transaksis','transaksis.id','=','pesanans.id_transaksi')
        ->join('menus','menus.id','=','pesanans.id_menu')
        ->join('mejas','mejas.id','=','reservasis.id_meja')
        ->join('users','users.id','=','reservasis.id_pegawai')
        ->join('pelanggans','pelanggans.id','=','reservasis.id_pelanggan')
        ->select('pesanans.*', 'mejas.nomor_meja','users.nama','menus.nama_menu','menus.tipe','pelanggans.nama_pelanggan')->where('reservasis.status','=',1)
        ->orderBy('pesanans.status_pesanan')
        ->get();

        if(count($pesanans)>0){
            return response([
                'message'=>'Retrieve All Success',
                'data'=>$pesanans
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data'=>null
        ],404);
    }

    public function show ($id){
        $pesanan=Pesanan::find($id);

        if(!is_null($pesanan)){
            return response([
                'message'  => 'Retrieve Reservasi Success',
                'data' => $pesanan
            ],200);

        }

        return response([
            'message' => 'Reservasi Not Found',
            'data' => null
        ],404);
    }

    //Semua Orderan dengan id nb:id disini adalah id user kayanya blm cari dengan id user ini
    public function showOrder($id_reservasi){
        $pesanans = DB::table('pesanans')
        ->join('reservasis', 'reservasis.id', '=', 'pesanans.id_reservasi')
        ->join('transaksis','transaksis.id','=','pesanans.id_transaksi')
        ->join('menus','menus.id','=','pesanans.id_menu')
        ->join('mejas','mejas.id','=','reservasis.id_meja')
        ->join('users','users.id','=','reservasis.id_pegawai')
        ->join('pelanggans','pelanggans.id','=','reservasis.id_pelanggan')
        ->select('pesanans.*', 'mejas.nomor_meja','users.nama','menus.nama_menu','menus.tipe','pelanggans.nama_pelanggan')->where('reservasis.id','=',$id_reservasi)
        ->orderBy('pesanans.status_pesanan')
        ->get();

        if(!is_null($pesanans)){
            return response([
                'message'=>'Retrieve Pesanan Success',
                'data'=>$pesanans
            ],200);
        }

        return response([
            'message'=>'Pesanan Not Found',
            'data'=>null
        ],404);
    }

    public function updateStatus(Request $request, $id){
        $pesanan = Pesanan::find($id);
        if(is_null($pesanan)){
            return response([
                'message'=>'Pesanan Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'status_pesanan' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

            $pesanan->status_pesanan = $updateData['status_pesanan'];

        if($pesanan->save()){
            return response([
                'message' => 'Data Pesanan berhasil diubah',
                'data'=> $pesanan,
            ],200);
        }

        return response([
            'messsage'=>'Delete Bahan Failed',
            'data'=>null,
        ],400);
    }

    //Create Pesanan baru cek
    public function store(Request $request){
        $storeData =$request->all();
        $id_reservasi = $storeData['id_reservasi'];
        $id_product = $storeData['id_product'];
        // return $storeData;

        //-------------------------------------------Belum dicoba---------------------------------------------------------//
        $product = Pesanan::where('id', $id_product)->first();
        if($product['stok_product']<$storeData['quantity']){
            return response([
                'message'=>'Stock Pesanan tidak cukup',
                'data'=>null,
            ],200);
        }

        //-------------------------------------------Belum dicoba---------------------------------------------------------//

        $pesanans = Pesanan::where([
            ['id_reservasi', $id_reservasi],
            ['id_product', $id_product]
        ])->first();

        // return $pesanans;

        if($pesanans!=null){
            return $this->update($request,$id_reservasi);
        }

        $validate = Validator::make($storeData,[
            'nama_product'=>'required',
            'harga_product'=>'required',
            'quantity'=>'required',
            'id_product'=>'required',
            'id_reservasi'=>'required'
        ]);
        $product['stok_product'] = $product['stok_product'] - $storeData['quantity'];
        $product->save();
        $storeData['total'] = $storeData['harga_product']*$storeData['quantity'];

        if($validate->fails()){
            return response(['message'=>$validate->errors()],400);
        }

        $pesanans = Pesanan::create($storeData);

        return response([
            'message'=>'Add Pesanan Success',
            'data'=>$pesanans,
        ],200);
    }

    //DELETE DENGAN Id_product
    public function destroy($id){
        $pesanans = Pesanan::where('id', $id)->first();
        // return $pesanans;
        $id_product = $pesanans['id_product'];
        $product = Pesanan::where('id', $id_product)->first();
        if(is_null($pesanans)){
            return response([
                'message'=>'Pesanan Not Found',
                'data'=>null
            ],404);
        }else{
            $product['stok_product'] = $product['stok_product'] + $pesanans['quantity'];
            $product->save();
        }

        //return message saat data order tidak ditemukan

        if($pesanans->delete()){
            return response([
                'message'=>'Delete Pesanan Success',
                'data'=>$pesanans,
            ],200);
        }//return message saat berhasil menghapus data

        return response([
            'message'=>'Delete Pesanan Failed',
            'data'=>null
        ],400);//return message saat gagal menghapus data order
    }

    //UPDATE SESUAI PRODUK BELUM sesuai
    public function update(Request $request, $id){
        $storeData =$request->all();
        $id_reservasi = $storeData['id_reservasi'];
        $id_product = $storeData['id_product'];
        // return $storeData;

        //-------------------------------------------Belum dicoba---------------------------------------------------------//
        $product = Pesanan::where('id', $id_product)->first();
        if($product['stok_product']<$storeData['quantity']){
            return response([
                'message'=>'Stock Pesanan tidak cukup',
                'data'=>null,
            ],200);
        }else{
            $product['stok_product'] = $product['stok_product'] - $storeData['quantity'];
            $product->save();
        }
        //-------------------------------------------Belum dicoba---------------------------------------------------------//

        $pesanans = Pesanan::where([
            ['id_reservasi', $id_reservasi],
            ['id_product', $id_product]
        ])->first();

        if(is_null($pesanans)){
            return response([
                'message'=>'Pesanan Not Found',
                'data'=>null
            ],404);
        }

        //validate update blm
        $validate = Validator::make($storeData,[
            'nama_product'=>'required',
            'harga_product'=>'required',
            'quantity'=>'required',
            // 'total'=>'required',
            'id_product'=>'required',
            'id_reservasi'=>'required'
        ]);

        if($validate->fails())
            return response(['message'=>$validate->errors()],404);//return error invalid input

        $qty = $pesanans['quantity'] + $storeData['quantity'];
        $totalHarga = $qty * $pesanans['harga_product'];

        $pesanans['nama_product'] = $storeData['nama_product'];
        $pesanans['harga_product'] = $storeData['harga_product'];
        $pesanans['quantity'] = $qty;
        $pesanans['total'] = $totalHarga;


        if($pesanans->save()){
            return response([
                'message'=>'Update Pesanan Success',
                'data'=>$pesanans,
            ],200);
        }//return order yg telah diedit
        // $pesanans = Pesanan::updateOrCreate($storeData);
        return response([
            'message'=>'Update success ',
            'data'=>$pesanans,
        ],404);//return message saat order gagal diedit
    }

    //update yg cart id disini adalah id order
    //ini mau buat fungsi update khusus untuk cart tapi ga work aneh
    public function updateCart(Request $request, $id){
        $pesanans = Pesanan::find($id);

        // return $pesanans;
        if(is_null($pesanans)){
            return response([
                'message'=>'Pesanan Not Found',
                'data'=>null
            ],404);
        }

        // return $storeData;
        $storeData = $request->all();

          //-------------------------------------------Belum dicoba---------------------------------------------------------//
          $product = Pesanan::where('id', $id_product)->first();
          if($product['stok_product']<$storeData['quantity']){
              return response([
                  'message'=>'Stock Pesanan tidak cukup',
                  'data'=>null,
              ],200);
          }
          //-------------------------------------------Belum dicoba---------------------------------------------------------//

        //validate update blm
        $validate = Validator::make($storeData,[
            'nama_product'=>'required',
            'harga_product'=>'required',
            'quantity'=>'required',
            // 'total'=>'required',
            'id_product'=>'required',
            'id_reservasi'=>'required'
        ]);

        if($validate->fails())
            return response(['message'=>$validate->errors()],400);//return error invalid input

        $qty = $pesanans['quantity'] + $storeData['quantity'];
        $totalHarga = $qty * $pesanans['harga_product'];

        $pesanans['nama_product'] = $storeData['nama_product'];
        $pesanans['harga_product'] = $storeData['harga_product'];
        $pesanans['quantity'] = $qty;
        $pesanans['total'] = $totalHarga;


        if($pesanans->save()){
            return response([
                'message'=>'Update Pesanan Success',
                'data'=>$pesanans,
            ],200);
        }//return order yg telah diedit
        // $pesanans = Pesanan::updateOrCreate($storeData);
        return response([
            'message'=>'Update success ',
            'data'=>$pesanans,
        ],404);//return message saat order gagal diedit
    }
}
