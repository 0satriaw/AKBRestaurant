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
                'message'  => 'Retrieve Pesanan Success',
                'data' => $pesanan
            ],200);

        }

        return response([
            'message' => 'Pesanan Not Found',
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
        ->select('pesanans.*', 'mejas.nomor_meja','users.nama','menus.nama_menu','menus.tipe','menus.gambar','pelanggans.nama_pelanggan')->where([['transaksis.id','=',$id_reservasi],['status_pesanan','5']])
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

    public function showPesanan($id_reservasi){
        $pesanans = DB::table('pesanans')
        ->join('reservasis', 'reservasis.id', '=', 'pesanans.id_reservasi')
        ->join('transaksis','transaksis.id','=','pesanans.id_transaksi')
        ->join('menus','menus.id','=','pesanans.id_menu')
        ->join('mejas','mejas.id','=','reservasis.id_meja')
        ->join('users','users.id','=','reservasis.id_pegawai')
        ->join('pelanggans','pelanggans.id','=','reservasis.id_pelanggan')
        ->select('pesanans.*', 'mejas.nomor_meja','menus.gambar','users.nama','menus.nama_menu','menus.tipe','pelanggans.nama_pelanggan')->where([['transaksis.id','=',$id_reservasi],['status_pesanan','!=','5']])
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
        $id_menu = $storeData['id_menu'];
        $id_transaksi = $storeData['id_transaksi'];
        // return $storeData;

        //-------------------------------------------Belum dicoba---------------------------------------------------------//
        $menu = Menu::where('id', $id_menu)->first();
        if($menu['stok']<$storeData['jumlah']){
            return response([
                'message'=>'Stok Pesanan tidak cukup',
                'data'=>null,
            ],200);
        }

        //-------------------------------------------Belum dicoba---------------------------------------------------------//

        $pesanans = Pesanan::where([
            ['id_reservasi', $id_reservasi],
            ['id_menu', $id_menu],
            ['id_transaksi',$id_transaksi],
            ['status_pesanan',5]
        ])->first();

        // return $pesanans;

        if($pesanans!=null){
            return $this->update($request,$id_reservasi);
        }

        $validate = Validator::make($storeData,[
            'id_reservasi'=>'required|numeric',
            'id_transaksi'=>'required|numeric',
            'id_menu'=>'required|numeric',
            'status_pesanan'=>'required|numeric',
            'jumlah'=>'required|numeric',
            'total_harga'=>'required|numeric'
        ]);
        $menu['stok'] = $menu['stok'] - $storeData['jumlah'];
        $menu->save();

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
        $id_menu = $pesanans['id_menu'];
        $menu = Menu::where('id', $id_menu)->first();
        if(is_null($pesanans)){
            return response([
                'message'=>'Pesanan Not Found',
                'data'=>null
            ],404);
        }else{
            $menu['stok'] = $menu['stok'] + $pesanans['jumlah'];
            $menu->save();
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
        $id_menu = $storeData['id_menu'];
        $id_transaksi = $storeData['id_transaksi'];
        // return $storeData;

        //-------------------------------------------Belum dicoba---------------------------------------------------------//
        $menu = Menu::where('id', $id_menu)->first();
        if($menu['stok']<$storeData['jumlah']){
            return response([
                'message'=>'Stock Pesanan tidak cukup',
                'data'=>null,
            ],200);
        }

        //-------------------------------------------Belum dicoba---------------------------------------------------------//

        $pesanans = Pesanan::where([
            ['id_reservasi', $id_reservasi],
            ['id_menu', $id_menu],
            ['id_transaksi',$id_transaksi],
            ['status_pesanan',5]
        ])->first();

        // return $pesanans;

        if(is_null($pesanans)){
            return response([
                'message'=>'Pesanan Not Found',
                'data'=>null
            ],404);
        }

        //validate update blm
        $validate = Validator::make($storeData,[
            'id_reservasi'=>'required|numeric',
            'id_transaksi'=>'required|numeric',
            'id_menu'=>'required|numeric',
            'status_pesanan'=>'required|numeric',
            'jumlah'=>'required|numeric',
            'total_harga'=>'required|numeric'
        ]);

        if($validate->fails())
            return response(['message'=>$validate->errors()],404);//return error invalid input

        $qty = $pesanans['jumlah'] + $storeData['jumlah'];
        $harga_menu = $pesanans['total_harga']/$pesanans['jumlah'];
        $totalHarga = $qty * $harga_menu;

        $pesanans['jumlah'] = $qty;
        $pesanans['total_harga'] = $totalHarga;

        $menu['stok'] = $menu['stok'] - $storeData['jumlah'];
        $menu->save();


        if($pesanans->save()){
            return response([
                'message'=>'Update Pesanan Success',
                'data'=>$pesanans,
            ],200);
        }//return order yg telah diedit
        // $pesanans = Pesanan::updateOrCreate($storeData);
        return response([
            'message'=>'Update Failed ',
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
        $id_menu = $storeData['id_menu'];

          //-------------------------------------------Belum dicoba---------------------------------------------------------//
          $menu = Menu::where('id', $id_menu)->first();
          if($menu['stok']<$storeData['jumlah']){
              return response([
                  'message'=>'Stock Pesanan tidak cukup',
                  'data'=>null,
              ],200);
          }

          //-------------------------------------------Belum dicoba---------------------------------------------------------//

        //validate update blm
        $validate = Validator::make($storeData,[
            'id_reservasi'=>'required|numeric',
            'id_transaksi'=>'required|numeric',
            'id_menu'=>'required|numeric',
            'status_pesanan'=>'required|numeric',
            'jumlah'=>'required|numeric',
            'total_harga'=>'required|numeric'
        ]);

        if($validate->fails())
            return response(['message'=>$validate->errors()],400);//return error invalid input

        $menu['stok'] = $menu['stok'] + $pesanans['jumlah'] - $storeData['jumlah'];
        $menu->save();
        
        $pesanans['jumlah'] = $storeData['jumlah'];
        $pesanans['total_harga'] = $menu['harga']*$storeData['jumlah'];

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

    public function updatePesanan(Request $request, $id){
        $storeData = $request->all();

        $pesanan = Pesanan::where([
            ['id_transaksi',$storeData['id_transaksi']],
            ['id_reservasi',$storeData['id_reservasi']],
            ['status_pesanan',5]])
            ->get();

        // return $pesanan;

        if(is_null($pesanan)){
            return response([
                'message'=>'Pesanan Not Found',
                'data'=>null
            ],404);
        }

        $pesanan = Pesanan::where([
            ['id_transaksi',$storeData['id_transaksi']],
            ['id_reservasi',$storeData['id_reservasi']],
            ['status_pesanan',5]])
            ->update(['status_pesanan'=> 0]);

        return response([
            'message' => 'Data Pesanan berhasil diubah',
            'data'=> $pesanan,
        ],200);
    }
}
