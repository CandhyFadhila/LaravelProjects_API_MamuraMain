<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'preferred_package_id',
    ];

    /**
     * Get the preferred_package that owns the Inquiry
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function preferred_package(): BelongsTo
    {
        return $this->belongsTo(Pricing::class, 'preferred_package_id', 'id');
    }
}
