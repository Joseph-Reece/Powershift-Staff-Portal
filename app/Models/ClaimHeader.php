<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimHeader extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyStaffClaimHeader";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 52202717,
            'pKeyID' => 1,
        ];
        return $data;
    }
}
