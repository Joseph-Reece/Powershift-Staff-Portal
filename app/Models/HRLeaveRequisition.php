<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HRLeaveRequisition extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyHRLeaveApplications";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 52202673,
            'pKeyID' => 1
        ];
        return $data;
    }
}
