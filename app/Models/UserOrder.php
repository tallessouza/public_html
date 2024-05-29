<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserOrder extends Model
{

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array'
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlans::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
