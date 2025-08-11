<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedAsset extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyFixedAssets";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 0,
        ];
        return $data;
    }
}
