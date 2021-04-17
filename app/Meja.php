<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Meja extends Model
{
    protected $fillable = [
        'nomor_meja',
        'status',
        'status_hapus'
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
