<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImprestSurrenderHeader extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyImprestSurrenderHeader";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 61504,
        ];
        return $data;
    }
}
