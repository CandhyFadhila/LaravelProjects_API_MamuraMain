<?php

namespace App\Models;

use App\Traits\HasArrayRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use SoftDeletes, HasArrayRelations;

    protected $guarded = ['id'];

    protected $appends = ['documents'];

    protected $casts = [
        'carrier_id' => 'integer',
        'resume_id' => 'array',
    ];

    /**
     * Get the carrier that owns the JobApplication
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class, 'carrier_id', 'id');
    }

    public function getDocumentsAttribute()
    {
        return $this->resolveArrayRelations(
            $this->resume_id,
            Document::class,
            ['uploaded_users', 'verified_users']
        );
    }
}
