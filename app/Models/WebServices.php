<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebServices extends Model
{
    use HasFactory;
    public static function AppraisalHeader(){
        return "PgHRAppraisalHeaderList";
    }
    public static function AppraisalScore(){
        return "QyBalancedScoreCardList";
    }
}
