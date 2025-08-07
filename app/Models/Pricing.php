<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pricing extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'pricing_category_id' => 'integer',
        'internet_speed' => 'integer',
        'price' => 'integer',
        'is_recommended' => 'integer'
    ];

    /**
     * Get the pricing_category that owns the Pricing
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pricing_category(): BelongsTo
    {
        return $this->belongsTo(PricingCategory::class, 'pricing_category_id', 'id');
    }
}
