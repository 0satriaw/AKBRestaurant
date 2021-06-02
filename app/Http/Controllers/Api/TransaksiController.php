<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Pesanan;
use App\Menu;
use App\StokKeluar;
use App\StokMasuk;
use App\Bahan;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;
use App\Transaksi;

class TransaksiController extends Controller
{
    public function index(){
        $transaksi = DB::Table('transaksis')
        ->join('reservasis','reservasis.id','transaksis.id_reservasi')
        ->join('mejas','mejas.id','reservasis.id_meja')
        ->join('users','users.id','reservasis.id_pegawai')
        ->join('pelanggans','pelanggans.id','reservasis.id_pelanggan')
        ->select('transaksis.*','mejas.nomor_meja','users.nama','pelanggans.nama_pelanggan')->where('status_transaksi','=',0)
        ->get();

        if(count($transaksi)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$transaksi
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function indexlunas(){
        $transaksi = DB::Table('transaksis')
        ->join('reservasis','reservasis.id','transaksis.id_reservasi')
        ->join('mejas','mejas.id','reservasis.id_meja')
        ->join('users','users.id','reservasis.id_pegawai')
        ->join('pelanggans','pelanggans.id','reservasis.id_pelanggan')
        ->select('transaksis.*','mejas.nomor_meja','users.nama','pelanggans.nama_pelanggan')->where('status_transaksi','=',1)
        ->get();

        if(count($transaksi)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$transaksi
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function show ($id){
        $transaksi= Transaksi::find($id);

        if(!is_null($transaksi)){
            return response([
                'message'  => 'Retrieve Transaksi Success',
                'data' => $transaksi
            ],200);

        }

        return response([
            'message' => 'Transaksi Not Found',
            'data' => null
        ],404);
    }


    public function update(Request $request, $id){
        $transaksi = Transaksi::where('id_reservasi',$id)->first();
        // return $transaksi;

        if(is_null($transaksi)){
            return response([
                'message'=>'Transaksi Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_kartu' => 'nullable|numeric',
            'metode_pembayaran' => 'required',
            'status_transaksi' => 'required|numeric',
        ]);

        if($validate->fails())
            return response(['message'=> $validate->errors()],400);

        if($updateData['id_kartu']!=null){
            $transaksi->id_kartu =  $updateData['id_kartu'];
        }
            $transaksi->metode_pembayaran = $updateData['metode_pembayaran'];
            $transaksi->status_transaksi = $updateData['status_transaksi'];
            $transaksi->tanggal_transaksi =  date('Y-m-d H:i:s');

        if($transaksi->save()){
            return response([
                'message' => 'Data Transaksi berhasil ditambah',
                'data'=> $transaksi,
            ],200);
        }

        return response([
            'messsage'=>'Gagal memproses transaksi',
            'data'=>null,
        ],400);
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





    ///MULAI DARI SINI COPAS

    public function getTahun(){
        $transaksi = DB::table('transaksis')
            ->select(DB::raw('YEAR(tanggal_transaksi) as tahun'))
            ->where('status_transaksi','=','1')
            ->groupBy('tahun')
            ->get();

        if(count($transaksi)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$transaksi
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function getTahunKeluar(){
        $transaksi = DB::table('stok_keluars')
            ->select(DB::raw('YEAR(tanggal_keluar) as tahun'))
            ->groupBy('tahun')
            ->get();

        if(count($transaksi)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$transaksi
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);

    }

    public function cetakStruk($id_reservasi){
            $pesanans = DB::table('pesanans')
            ->join('reservasis', 'reservasis.id', '=', 'pesanans.id_reservasi')
            ->join('transaksis','transaksis.id','=','pesanans.id_transaksi')
            ->join('menus','menus.id','=','pesanans.id_menu')
            ->join('mejas','mejas.id','=','reservasis.id_meja')
            ->join('users','users.id','=','reservasis.id_pegawai')
            ->join('pelanggans','pelanggans.id','=','reservasis.id_pelanggan')
            ->select('pesanans.*', 'mejas.nomor_meja','menus.gambar','users.nama','menus.nama_menu','menus.harga','menus.tipe','pelanggans.nama_pelanggan',
            'transaksis.tanggal_transaksi','transaksis.nomor_nota','transaksis.total_transaksi')->where([['transaksis.id_reservasi','=',$id_reservasi],['status_pesanan','!=','5']])
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


    public function laporanPendapatanPerbulan($tahun){
        $coll = new Collection();

        if($tahun < 2020){
            return response([
                'message' => 'Tahun harus lebih besar dari 2020',
                'data' => null,
            ],400);
        }

        $months = array(
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli ',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        );


        for ($bln=1; $bln <= 12; $bln++) {
            $makananU[$bln] = DB::table('pesanans')->join('menus', 'pesanans.id_menu', '=', 'menus.id')
                ->selectRaw('ifnull(sum(pesanans.total_harga), 0) as makananUBln')
                ->where('menus.tipe', '=', 'Makanan Utama')
                ->where('pesanans.status_pesanan', '!=', '5')
                // ->where('menus.status_hapus', '=', 0)
                ->whereMonth('pesanans.created_at', '=', $bln)
                ->whereYear('pesanans.created_at', '=', $tahun)
                ->first();

            $sidedish[$bln] = DB::table('pesanans')->join('menus', 'pesanans.id_menu', '=', 'menus.id')
                ->selectRaw('ifnull(sum(pesanans.total_harga), 0) as sidedishBln')
                ->where('menus.tipe', '=', 'Makanan Side Dish')
                ->where('pesanans.status_pesanan', '!=', '5')
                // ->where('menus.status_hapus', '=', 0)
                ->whereMonth('pesanans.created_at', '=', $bln)
                ->whereYear('pesanans.created_at', '=', $tahun)
                ->first();

            $minuman[$bln] = DB::table('pesanans')->join('menus', 'pesanans.id_menu', '=', 'menus.id')
                ->selectRaw('ifnull(sum(pesanans.total_harga), 0) as minumanBln')
                ->where('menus.tipe', '=', 'Minuman')
                ->where('pesanans.status_pesanan', '!=', '5')
                // ->where('menus.status_hapus', '=', 0)
                ->whereMonth('pesanans.created_at', '=', $bln)
                ->whereYear('pesanans.created_at', '=', $tahun)
                ->first();

            $total_pendapatan[$bln] = $makananU[$bln]->makananUBln + $sidedish[$bln]->sidedishBln + $minuman[$bln]->minumanBln;

            $laporan[$bln] = array(
                "No" => $bln,
                "Bulan" => $months[$bln-1],
                "Makanan" => $makananU[$bln]->makananUBln,
                "Sidedish" => $sidedish[$bln]->sidedishBln,
                "Minuman" => $minuman[$bln]->minumanBln,
                "Total" => $total_pendapatan[$bln]
            );
            $coll->push($laporan[$bln]);
        }

        return response([
            'message' => 'Tampil laporan pendapatan bulanan berhasil',
            'data' => $coll,
            ],200);
    }

    public function laporanPendapatanPertahun($tahun1,$tahun2){
        $coll = new Collection();

        if($tahun1 < 2020){
            return response([
                'message' => 'Tahun harus lebih besar dari 2020',
                'data' => null,
            ],400);
        }

        if($tahun2 < $tahun1){
            return response([
                'message' => 'Tahun awal harus lebih kecil dari tahun awal',
                'data' => null,
            ],400);
        }


        $thn = $tahun2-$tahun1 +1;
        $temp = $tahun1;


        for ($bln=1; $bln <= $thn; $bln++) {
            $makananU[$bln] = DB::table('pesanans')->join('menus', 'pesanans.id_menu', '=', 'menus.id')
                ->selectRaw('ifnull(sum(pesanans.total_harga), 0) as makananUBln')
                ->where('menus.tipe', '=', 'Makanan Utama')
                ->where('pesanans.status_pesanan', '!=', '5')
                // ->where('menus.status_hapus', '=', 0)
                // ->whereMonth('pesanans.created_at', '=', $bln)
                ->whereYear('pesanans.created_at', '=', $temp)
                ->first();

            $sidedish[$bln] = DB::table('pesanans')->join('menus', 'pesanans.id_menu', '=', 'menus.id')
                ->selectRaw('ifnull(sum(pesanans.total_harga), 0) as sidedishBln')
                ->where('menus.tipe', '=', 'Makanan Side Dish')
                ->where('pesanans.status_pesanan', '!=', '5')
                // ->where('menus.status_hapus', '=', 0)
                // ->whereMonth('pesanans.created_at', '=', $bln)
                ->whereYear('pesanans.created_at', '=', $temp)
                ->first();

            $minuman[$bln] = DB::table('pesanans')->join('menus', 'pesanans.id_menu', '=', 'menus.id')
                ->selectRaw('ifnull(sum(pesanans.total_harga), 0) as minumanBln')
                ->where('menus.tipe', '=', 'Minuman')
                ->where('pesanans.status_pesanan', '!=', '5')
                // ->where('menus.status_hapus', '=', 0)
                // ->whereMonth('pesanans.created_at', '=', $bln)
                ->whereYear('pesanans.created_at', '=', $temp)
                ->first();

            $total_pendapatan[$bln] = $makananU[$bln]->makananUBln + $sidedish[$bln]->sidedishBln + $minuman[$bln]->minumanBln;

            $laporan[$bln] = array(
                "No" => $bln,
                "Tahun" => $temp,
                "Makanan" => $makananU[$bln]->makananUBln,
                "Sidedish" => $sidedish[$bln]->sidedishBln,
                "Minuman" => $minuman[$bln]->minumanBln,
                "Total" => $total_pendapatan[$bln]
            );
            $temp = $temp+1;
            $temp = (string)$temp;
            $coll->push($laporan[$bln]);
        }

        return response([
            'message' => 'Tampil laporan pendapatan bulanan berhasil',
            'data' => $coll,
            ],200);
    }

    public function laporanPengeluaranPerbulan($tahun){
        $coll = new Collection();

        if($tahun < 2020){
            return response([
                'message' => 'Tahun harus lebih besar dari 2020',
                'data' => null,
            ],400);
        }


        $months = array(
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli ',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        );


        for ($bln=1; $bln <= 12; $bln++) {
            $makananU[$bln] = DB::table('stok_masuks')
                ->join('bahans', 'stok_masuks.id_bahan', 'bahans.id')
                ->join('menus', 'bahans.id', '=', 'menus.id_bahan')
                ->selectRaw('ifnull(sum(stok_masuks.biaya), 0) as makananUBln')
                ->where('menus.tipe', '=', 'Makanan Utama')
                ->where('stok_masuks.status_hapus', '!=', 1)
                ->where('menus.status_hapus', '=', 0)
                ->where('bahans.status_hapus', '=', 0)
                ->whereMonth('stok_masuks.tanggal_masuk', '=', $bln)
                ->whereYear('stok_masuks.tanggal_masuk', '=', $tahun)
                ->first();

            $sidedish[$bln] =  DB::table('stok_masuks')
                ->join('bahans', 'stok_masuks.id_bahan', 'bahans.id')
                ->join('menus', 'bahans.id', '=', 'menus.id_bahan')
                ->selectRaw('ifnull(sum(stok_masuks.biaya), 0) as sidedishBln')
                ->where('menus.tipe', '=', 'Makanan Side Dish')
                ->where('stok_masuks.status_hapus', '!=', 1)
                ->where('menus.status_hapus', '=', 0)
                ->where('bahans.status_hapus', '=', 0)
                ->whereMonth('stok_masuks.tanggal_masuk', '=', $bln)
                ->whereYear('stok_masuks.tanggal_masuk', '=', $tahun)
                ->first();

            $minuman[$bln] =  DB::table('stok_masuks')
                ->join('bahans', 'stok_masuks.id_bahan', 'bahans.id')
                ->join('menus', 'bahans.id', '=', 'menus.id_bahan')
                ->selectRaw('ifnull(sum(stok_masuks.biaya), 0) as minumanBln')
                ->where('menus.tipe', '=', 'Minuman')
                ->where('stok_masuks.status_hapus', '!=', 1)
                ->where('menus.status_hapus', '=', 0)
                ->where('bahans.status_hapus', '=', 0)
                ->whereMonth('stok_masuks.tanggal_masuk', '=', $bln)
                ->whereYear('stok_masuks.tanggal_masuk', '=', $tahun)
                ->first();

            $total_pengeluaran[$bln] = $makananU[$bln]->makananUBln + $sidedish[$bln]->sidedishBln + $minuman[$bln]->minumanBln;

            $laporan[$bln] = array(
                "No" => $bln,
                "Bulan" => $months[$bln-1],
                "Makanan" => $makananU[$bln]->makananUBln,
                "Sidedish" => $sidedish[$bln]->sidedishBln,
                "Minuman" => $minuman[$bln]->minumanBln,
                "Total Pengeluaran" => $total_pengeluaran[$bln]
            );
            $coll->push($laporan[$bln]);
        }

        return response([
            'message' => 'Tampil laporan pengeluaran bulanan berhasil',
            'data' => $coll,
            ],200);
    }

    public function laporanPengeluaranPertahun($tahun1,$tahun2){
        $coll = new Collection();

        if($tahun1 < 2020){
            return response([
                'message' => 'Tahun harus lebih besar dari 2020',
                'data' => null,
            ],400);
        }

        if($tahun2 < $tahun1){
            return response([
                'message' => 'Tahun awal harus lebih kecil dari tahun awal',
                'data' => null,
            ],400);
        }


        $thn = $tahun2-$tahun1 +1;
        $temp = $tahun1;


        for ($bln=1; $bln <= $thn; $bln++) {
            $makananU[$bln] = DB::table('stok_masuks')
                ->join('bahans', 'stok_masuks.id_bahan', 'bahans.id')
                ->join('menus', 'bahans.id', '=', 'menus.id_bahan')
                ->selectRaw('ifnull(sum(stok_masuks.biaya), 0) as makananUBln')
                ->where('menus.tipe', '=', 'Makanan Utama')
                ->where('stok_masuks.status_hapus', '!=', 1)
                ->where('menus.status_hapus', '=', 0)
                ->where('bahans.status_hapus', '=', 0)
                ->whereYear('stok_masuks.tanggal_masuk', '=', $temp)
                ->first();

            $sidedish[$bln] =  DB::table('stok_masuks')
                ->join('bahans', 'stok_masuks.id_bahan', 'bahans.id')
                ->join('menus', 'bahans.id', '=', 'menus.id_bahan')
                ->selectRaw('ifnull(sum(stok_masuks.biaya), 0) as sidedishBln')
                ->where('menus.tipe', '=', 'Makanan Side Dish')
                ->where('stok_masuks.status_hapus', '!=', 1)
                ->where('menus.status_hapus', '=', 0)
                ->where('bahans.status_hapus', '=', 0)
                ->whereYear('stok_masuks.tanggal_masuk', '=', $temp)
                ->first();

            $minuman[$bln] =  DB::table('stok_masuks')
                ->join('bahans', 'stok_masuks.id_bahan', 'bahans.id')
                ->join('menus', 'bahans.id', '=', 'menus.id_bahan')
                ->selectRaw('ifnull(sum(stok_masuks.biaya), 0) as minumanBln')
                ->where('menus.tipe', '=', 'Minuman')
                ->where('stok_masuks.status_hapus', '!=', 1)
                ->where('menus.status_hapus', '=', 0)
                ->where('bahans.status_hapus', '=', 0)
                ->whereYear('stok_masuks.tanggal_masuk', '=', $temp)
                ->first();

            $total_pendapatan[$bln] = $makananU[$bln]->makananUBln + $sidedish[$bln]->sidedishBln + $minuman[$bln]->minumanBln;

            $laporan[$bln] = array(
                "No" => $bln,
                "Tahun" => $temp,
                "Makanan" => $makananU[$bln]->makananUBln,
                "Sidedish" => $sidedish[$bln]->sidedishBln,
                "Minuman" => $minuman[$bln]->minumanBln,
                "Total" => $total_pendapatan[$bln]
            );
            $temp = $temp+1;
            $temp = (string)$temp;
            $coll->push($laporan[$bln]);
        }

        return response([
            'message' => 'Tampil laporan pendapatan bulanan berhasil',
            'data' => $coll,
            ],200);
    }

    public function laporanPenjualan($dt){
        $coll1 = new Collection();
        $coll2 = new Collection();
        $coll3 = new Collection();
        $menus = Menu::where('menus.status_hapus', '=', 0)->get();
        $range = $menus->count();
        $Shari = Carbon::parse($dt)->daysInMonth;
        // $Shari = Carbon::parse($dt)->daysInYear;
        $hari = (int)$Shari;
        $thn = Carbon::parse($dt)->format('Y');
        $bln = Carbon::parse($dt)->format('m');


        for ($i=1; $i <= $range; $i++) {

            $total[$i] = 0;
            $item = [];
            $num[] =0;

            for ($j=1; $j <= $hari; $j++) {
                if ($j >= 10) {
                    $date = $thn.'-'.$bln.'-'.$j;
                } else {
                    $date = $thn.'-'.$bln.'-0'.$j;
                }

                $item[$j] = DB::table('pesanans')->join('menus', 'pesanans.id_menu', '=', 'menus.id')
                    ->selectRaw('ifnull(sum(pesanans.jumlah), 0) as perHari')
                    ->where('menus.nama_menu', '=', $menus[$i-1]->nama_menu)
                    ->where('menus.status_hapus', '=', 0)
                    ->whereDate('pesanans.created_at', '=', $date)
                    ->first();

                $total[$i] = $total[$i] + $item[$j]->perHari;
            }

            $max = max($item);

            if ($menus[$i-1]->tipe == 'Makanan Utama') {
                $num[0] = $num[0] + 1;
                $no = $num[0];
            } else if ($menus[$i-1]->tipe == 'Makanan Side Dish') {
                $num[1] = $num[1] + 1;
                $no = $num[1];
            } else {
                $num[2] = $num[2] + 1;
                $no = $num[2];
            }

            $laporan[$i] = array(
                "No"=>$no,
                "Item_Menu" => $menus[$i-1]->nama_menu,
                "Unit" => $menus[$i-1]->unit,
                "Tipe" => $menus[$i-1]->tipe,
                "Penjualan_Tertinggi" => $max->perHari,
                "Total_Penjualan" => $total[$i]
            );
            if($menus[$i-1]->tipe=='Makanan Utama'){
                $coll1->push($laporan[$i]);
            }else if($menus[$i-1]->tipe=='Makanan Side Dish'){
                $coll2->push($laporan[$i]);
            }else{
                $coll3->push($laporan[$i]);
            }
        }

        return response([
            'message' => 'Tampil laporan penjualan item menus berhasil',
            'data1' => $coll1,
            'data2' => $coll2,
            'data3' => $coll3,
            ],200);
    }

    public function laporanPenjualanPertahun($dt){
        $coll1 = new Collection();
        $coll2 = new Collection();
        $coll3 = new Collection();
        $menus = Menu::where('menus.status_hapus', '=', 0)->get();
        $range = $menus->count();
        $Shari = Carbon::parse($dt)->daysInMonth;
        // $Shari = Carbon::parse($dt)->daysInYear;
        $hari = (int)$Shari;
        $thn = Carbon::parse($dt)->format('Y');
        // $bln = Carbon::parse($dt)->format('m');


        for ($i=1; $i <= $range; $i++) {

            $total[$i] = 0;
            $item = [];

            $num[] = 0;

            for ($k=1; $k <=12 ; $k++) {
                for ($j=1; $j <= $hari; $j++) {
                    if ($j >= 10) {
                        if($k>=10){
                            $date = $thn.'-'.$k.'-'.$j;
                        }
                        else{
                            $date = $thn.'-0'.$k.'-'.$j;
                        }
                    } else {
                        if($k>=10){
                            $date = $thn.'-'.$k.'-0'.$j;
                        }else{
                            $date = $thn.'-0'.$k.'-0'.$j;
                        }
                    }

                    $item[$j] = DB::table('pesanans')->join('menus', 'pesanans.id_menu', '=', 'menus.id')
                        ->selectRaw('ifnull(sum(pesanans.jumlah), 0) as perHari')
                        ->where('menus.nama_menu', '=', $menus[$i-1]->nama_menu)
                        ->where('menus.status_hapus', '=', 0)
                        ->whereDate('pesanans.created_at', '=', $date)
                        ->first();

                    $total[$i] = $total[$i] + $item[$j]->perHari;
                    $max = max($item);
                }
                $temp[$k] =$max;
            }

            $maxs = max($temp);

            if ($menus[$i-1]->tipe == 'Makanan Utama') {
                $num[0] = $num[0] + 1;
                $no = $num[0];
            } else if ($menus[$i-1]->tipe == 'Makanan Side Dish') {
                $num[1] = $num[1] + 1;
                $no = $num[1];
            } else {
                $num[2] = $num[2] + 1;
                $no = $num[2];
            }

            $laporan[$i] = array(
                "No" => $no,
                "Item_Menu" => $menus[$i-1]->nama_menu,
                "Unit" => $menus[$i-1]->unit,
                "Tipe" => $menus[$i-1]->tipe,
                "Penjualan_Tertinggi" => $maxs->perHari,
                "Total_Penjualan" => $total[$i]
            );

            if($menus[$i-1]->tipe=='Makanan Utama'){
                $coll1->push($laporan[$i]);
            }else if($menus[$i-1]->tipe=='Makanan Side Dish'){
                $coll2->push($laporan[$i]);
            }else{
                $coll3->push($laporan[$i]);
            }
        }

        return response([
            'message' => 'Tampil laporan penjualan item menus berhasil',
            'data1' => $coll1,
            'data2' => $coll2,
            'data3' => $coll3,
            ],200);
    }

    public function getNamaMenu(){
        $transaksi = DB::table('menus')
            ->select('nama_menu')
            ->where('status_hapus','=',0)
            ->get();

        if(count($transaksi)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$transaksi
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);
    }

    public function laporanStokPerbulan($tgl1,$nama_menu){
        $coll1 = new Collection();
        $menus = DB::table('menus')
            ->join('bahans', 'menus.id_bahan', '=', 'bahans.id')
            ->select('menus.id', 'menus.id_bahan', 'bahans.nama_bahan', 'menus.nama_menu',
                'menus.tipe', 'menus.status_hapus', 'bahans.satuan')
            ->where('menus.status_hapus', '=', 0)
            ->where('menus.nama_menu','=',$nama_menu)
            ->first();

        $Shari = Carbon::parse($tgl1)->daysInMonth;
        $hari = (int)$Shari;
        $thn = Carbon::parse($tgl1)->format('Y');
        $bln = Carbon::parse($tgl1)->format('m');


            for ($i=1; $i <= $hari; $i++) {
                if ($i >= 10) {
                    $date = $thn.'-'.$bln.'-'.$i;
                } else {
                    $date = $thn.'-'.$bln.'-0'.$i;
                }

                $incoming[$i] = DB::table('stok_masuks')
                    ->join('bahans', 'stok_masuks.id_bahan', 'bahans.id')
                    ->join('menus', 'menus.id_bahan', 'bahans.id')
                    ->selectRaw('ifnull(sum(stok_masuks.jumlah), 0) as incoming')
                    ->where('menus.nama_menu', '=', $menus->nama_menu)
                    ->where('menus.status_hapus', '=', 0)
                    ->where('bahans.status_hapus', '=', 0)
                    ->where('stok_masuks.status_hapus', '!=', 1)
                    ->whereDate('stok_masuks.tanggal_masuk', $date)
                    ->first();

                $waste[$i] = DB::table('stok_keluars')->join('bahans', 'stok_keluars.id_bahan', 'bahans.id')
                    ->join('menus', 'menus.id_bahan', 'bahans.id')
                    ->selectRaw('ifnull(sum(stok_keluars.jumlah), 0) as waste')
                    ->where('menus.nama_menu', '=', $menus->nama_menu)
                    ->where('menus.status_hapus', '=', 0)
                    ->where('bahans.status_hapus', '=', 0)
                    ->where('stok_keluars.status', '=', 0)
                    ->whereDate('stok_keluars.tanggal_keluar', $date)
                    ->first();

                $use[$i] = DB::table('stok_keluars')->join('bahans', 'stok_keluars.id_bahan', 'bahans.id')
                    ->join('menus', 'menus.id_bahan', 'bahans.id')
                    ->selectRaw('ifnull(sum(stok_keluars.jumlah), 0) as waste')
                    ->where('menus.nama_menu', '=', $menus->nama_menu)
                    ->where('menus.status_hapus', '=', 0)
                    ->where('bahans.status_hapus', '=', 0)
                    ->where('stok_keluars.status', '=', 1)
                    ->whereDate('stok_keluars.tanggal_keluar', $date)
                    ->first();


                $laporan[$i] = array(
                    "No" => $i,
                    "Tanggal" => Carbon::parse($date)->format('d M Y'),
                    "Satuan" => $menus->satuan,
                    "Incoming_Stok" => $incoming[$i]->incoming,
                    "Remaining_Stok" => $incoming[$i]->incoming - $use[$i]->waste,
                    "Waste_Stok" => $waste[$i]->waste,
                );
                $coll1->push($laporan[$i]);
            }

        return response([
            'message' => 'Tampil laporan penjualan item menus berhasil',
            'data1' => $coll1,
            ],200);
    }

    public function laporanStok($tgl1,$tgl2){
        $coll1 = new Collection();
        $coll2 = new Collection();
        $coll3 = new Collection();
        $menus = DB::table('menus')
            ->join('bahans', 'menus.id_bahan', '=', 'bahans.id')
            ->select('menus.id', 'menus.id_bahan', 'bahans.nama_bahan', 'menus.nama_menu',
                'menus.tipe', 'menus.status_hapus', 'bahans.satuan')->where('menus.status_hapus', '=', 0)
            ->get();

        $range = $menus->count();

        $awal = date($tgl1);
        $akhir = date($tgl2);

        for ($i=1; $i <= $range; $i++) {

            $num[] = 0;

            $incoming[$i] = DB::table('stok_masuks')
                ->join('bahans', 'stok_masuks.id_bahan', 'bahans.id')
                ->join('menus', 'menus.id_bahan', 'bahans.id')
                ->selectRaw('ifnull(sum(stok_masuks.jumlah), 0) as incoming')
                ->where('menus.nama_menu', '=', $menus[$i-1]->nama_menu)
                ->where('menus.status_hapus', '=', 0)
                ->where('bahans.status_hapus', '=', 0)
                ->where('stok_masuks.status_hapus', '!=', 1)
                ->whereBetween('stok_masuks.tanggal_masuk', [$awal, $akhir])
                ->first();

            $waste[$i] = DB::table('stok_keluars')->join('bahans', 'stok_keluars.id_bahan', 'bahans.id')
                ->join('menus', 'menus.id_bahan', 'bahans.id')
                ->selectRaw('ifnull(sum(stok_keluars.jumlah), 0) as waste')
                ->where('menus.nama_menu', '=', $menus[$i-1]->nama_menu)
                ->where('menus.status_hapus', '=', 0)
                ->where('bahans.status_hapus', '=', 0)
                ->where('stok_keluars.status', '=', 0)
                ->whereBetween('stok_keluars.tanggal_keluar', [$awal, $akhir])
                ->first();

            $use[$i] = DB::table('stok_keluars')->join('bahans', 'stok_keluars.id_bahan', 'bahans.id')
                ->join('menus', 'menus.id_bahan', 'bahans.id')
                ->selectRaw('ifnull(sum(stok_keluars.jumlah), 0) as waste')
                ->where('menus.nama_menu', '=', $menus[$i-1]->nama_menu)
                ->where('menus.status_hapus', '=', 0)
                ->where('bahans.status_hapus', '=', 0)
                ->where('stok_keluars.status', '=', 1)
                ->whereBetween('stok_keluars.tanggal_keluar', [$awal, $akhir])
                ->first();

                if ($menus[$i-1]->tipe == 'Makanan Utama') {
                    $num[0] = $num[0] + 1;
                    $no = $num[0];
                } else if ($menus[$i-1]->tipe == 'Makanan Side Dish') {
                    $num[1] = $num[1] + 1;
                    $no = $num[1];
                } else {
                    $num[2] = $num[2] + 1;
                    $no = $num[2];
                }

            $laporan[$i] = array(
                "No" => $no,
                "Item_Menu" => $menus[$i-1]->nama_menu,
                "Satuan" => $menus[$i-1]->satuan,
                "Incoming_Stok" => $incoming[$i]->incoming,
                "Remaining_Stok" => $incoming[$i]->incoming - $use[$i]->waste,
                "Waste_Stok" => $waste[$i]->waste,
                "Tipe" => $menus[$i-1]->tipe
            );

            if($menus[$i-1]->tipe=='Makanan Utama'){
                $coll1->push($laporan[$i]);
            }else if($menus[$i-1]->tipe=='Makanan Side Dish'){
                $coll2->push($laporan[$i]);
            }else{
                $coll3->push($laporan[$i]);
            }

        }
        return response([
            'message' => 'Tampil laporan penjualan item menus berhasil',
            'data1' => $coll1,
            'data2' => $coll2,
            'data3' => $coll3,
            ],200);
    }
}
