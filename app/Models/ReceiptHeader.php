<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptHeader extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyReceiptsHeader";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 38,
        ];
        return $data;
    }
}
