<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPlans extends Model
{
    protected $table = 'plans';

    protected $guarded = [];

    protected $casts = [
      'open_ai_items' => 'json'
    ];

    // gateway_products
    public function gateway_products()
    {
        return $this->hasMany(GatewayProducts::class, 'plan_id', 'id');
    }

    // revenuecat_products
    public function revenuecat_products()
    {
        return $this->hasMany(RevenueCatProducts::class, 'plan_id', 'id');
    }

    public function checkOpenAiItemCount(): int
    {
        $items = is_array($this->open_ai_items) ? $this->open_ai_items : [];

        return count($items);
    }

    public function checkOpenAiItem($key): bool
    {
        $items = $this->open_ai_items ?: [];

        return in_array($key, $items);
    }
}
