<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $appends = ['documents'];

    protected $casts = [
        'blog_category_id' => 'integer',
        'thumbnail_id' => 'array',
    ];

    /**
     * Get the blog_category that owns the Blog
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function blog_category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id', 'id');
    }

    public function getDocumentsAttribute()
    {
        return $this->resolveArrayRelations(
            $this->thumbnail_id,
            Document::class,
            ['uploaded_users', 'verified_users']
        );
    }
}
