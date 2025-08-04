<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $appends = ['documents'];

    protected $casts = [
        'content_type_id' => 'integer',
        'content_file_id' => 'array',
    ];

    /**
     * Get the content_type that owns the Blog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function content_type(): BelongsTo
    {
        return $this->belongsTo(ContentType::class, 'content_type_id', 'id');
    }

    public function getDocumentsAttribute()
    {
        return $this->resolveArrayRelations(
            $this->content_file_id,
            Document::class,
            ['uploaded_users', 'verified_users']
        );
    }
}
