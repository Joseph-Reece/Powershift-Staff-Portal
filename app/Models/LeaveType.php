<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    const ANNUAL = 'ANNUAL';
    const SICK = 'SICK';

    public static function wsName(){
        return "PgLeaveTypes";
    }
}
