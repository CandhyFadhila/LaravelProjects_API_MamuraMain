<?php

namespace App\Models;

use App\Helpers\DateHelper;
use App\Traits\HasArrayRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use SoftDeletes, HasArrayRelations;

    protected $guarded = ['id'];

    protected $appends = ['documents'];

    protected $casts = [
        'promo_banner_id' => 'array',
        'terms' => 'array',
        'promo_value' => 'integer',
        'promo_end' => 'datetime',
    ];

    public function getDocumentsAttribute()
    {
        return $this->resolveArrayRelations(
            $this->promo_banner_id,
            Document::class,
            ['uploaded_users', 'verified_users']
        );
    }

    public function setPromoEndAttribute($value)
    {
        $this->attributes['promo_end'] = DateHelper::toDatabaseUTC($value);
    }
}
