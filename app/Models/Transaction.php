<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id', 'referrer_level_1', 'referrer_level_2', 'referrer_level_3',
        'purchase_amount', 'level_1_commission', 'level_2_commission', 'level_3_commission', 'metadata'
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function referrerLevel1()
    {
        return $this->belongsTo(User::class, 'referrer_level_1');
    }

    public function referrerLevel2()
    {
        return $this->belongsTo(User::class, 'referrer_level_2');
    }

    public function referrerLevel3()
    {
        return $this->belongsTo(User::class, 'referrer_level_3');
    }
}
