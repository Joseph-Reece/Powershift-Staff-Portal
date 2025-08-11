<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequisitionHeader extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyPurchaseHeader";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 52121800,
        ];
        return $data;
    }
}
