<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PricingCategory extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    /**
     * Get all of the pricings for the PricingCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pricings(): HasMany
    {
        return $this->hasMany(Pricing::class);
    }
}
