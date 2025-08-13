<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HRLeavePeriod extends Model
{
    use HasFactory;
    public static function wsName(){
        return "PgHrLeaveCalendar";
    }
    public static function tableDesc(){
        $data =  [
            'tableID' => 52203100,
        ];
        return $data;
    }
}
