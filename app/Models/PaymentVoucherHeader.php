<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentVoucherHeader extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyPaymentsHeader";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 50000,
        ];
        return $data;
    }
}
