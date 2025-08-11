<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImprestHeader extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyImprestHeader";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 52121500,
        ];
        return $data;
    }
}
