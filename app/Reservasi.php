<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Reservasi extends Model
{
    protected $fillable = [
        'id_pelanggan',
        'id_meja',
        'id_pegawai',
        'kode_qr',
        'tanggal_kunjungan',
        'jam_kunjungan',
        'sesi',
        'status_hapus',
        'status'
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
