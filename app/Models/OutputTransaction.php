<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutputTransaction extends Model
{
    use HasFactory;


    protected $fillable = [
        'id',
        'item_id',
        'issued_qty',
        'balance',
    ];


      public function item()
    {
        return $this->belongsTo('App\Models\Output');
    }
}
