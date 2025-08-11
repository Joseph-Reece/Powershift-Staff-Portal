<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRequisition extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyTransportRequisition";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 61801,
        ];
        return $data;
    }
}
