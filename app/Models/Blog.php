<?php

namespace App\Models;

use App\Traits\HasArrayRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Blog extends Model
{
    use SoftDeletes, HasArrayRelations;

    protected $guarded = ['id'];

    protected $appends = ['documents', 'thumbnail_url'];

    protected $casts = [
        'blog_category_id' => 'integer',
        'thumbnail_id' => 'array',
        'views' => 'integer'
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

    // => URL publik untuk OG/Twitter image
    public function getThumbnailUrlAttribute(): ?string
    {
        $first = collect($this->documents)->first();
        if (!$first) return null;

        // dukung array/objek
        $path = is_array($first) ? ($first['path'] ?? null) : ($first->path ?? null);
        return $path ? Storage::url($path) : null;
    }
}
