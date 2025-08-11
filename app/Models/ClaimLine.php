<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimLine extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyStaffClaimLines";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 61603,
            'pKeyID' => 1,
        ];
        return $data;
    }
}
