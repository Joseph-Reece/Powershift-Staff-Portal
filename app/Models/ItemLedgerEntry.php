<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemLedgerEntry extends Model
{
    use HasFactory;
    public static function wsName(){
        return "QyItemLedgerEntry";
    }
}
