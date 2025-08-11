<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreRequisitionHeader extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyStoreRequisitionHeader";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 52121800,
        ];
        return $data;
    }
}
