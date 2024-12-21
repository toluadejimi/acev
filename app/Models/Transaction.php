<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;


    protected $fillable = [
        'user_id',
        'amount',
        'ref_id',
        'status',
        'status',
        'type'


    ];
    public function item()
    {
        return $this->belongsTo('App\Models\Item');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
