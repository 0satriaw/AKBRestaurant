<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Transaksi extends Model
{
    protected $fillable = [
        'id_reservasi',
        'id_kartu',
        'id_pegawai',
        'metode_pembayaran',
        'total_transaksi',
        'tanggal_transaksi',
        'status_transaksi',
        'kode_transaksi',
        'nomor_nota',
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }
}
