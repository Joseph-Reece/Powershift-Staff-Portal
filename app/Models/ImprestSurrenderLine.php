<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImprestSurrenderLine extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyImprestSurrenderLines";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 0,
        ];
        return $data;
    }
}
