<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    use HasFactory;


    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }


    protected $fillable = [
        'user_id',
        'phone',
        'order_id',
        'country',
        'service',
        'expires_in',
        'cost',
        'api_cost',
        'status',
        'type',
    ];



}
