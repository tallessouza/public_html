<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscriptions extends Model
{
    protected $table = 'subscriptions';

    protected $fillable = [
        'stripe_status',
        'plan_id'
    ];


    public function plan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlans::class, 'plan_id');
    }    
}
