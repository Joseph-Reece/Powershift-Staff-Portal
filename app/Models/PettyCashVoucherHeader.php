<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PettyCashVoucherHeader extends Model
{
    use HasFactory;
    public static function wsName(){
        return "PgPettyCashHeader";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 52121505,
        ];
        return $data;
    }
}
