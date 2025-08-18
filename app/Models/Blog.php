<?php

namespace App\Models;

use App\Traits\HasArrayRelations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    protected function normalizeDocUrl(?string $maybeUrlOrPath): ?string
    {
        if (!$maybeUrlOrPath) return null;
        if (Str::startsWith($maybeUrlOrPath, ['http://', 'https://'])) {
            return $maybeUrlOrPath;
        }

        // Prioritas pakai base URL doc server; fallback ke Storage::url()
        $base = rtrim(config('documents.base_url', ''), '/');
        if ($base !== '') {
            return $base . '/' . ltrim($maybeUrlOrPath, '/');
        }

        // fallback terakhir (jika file memang di-disk publik lokal)
        return Storage::url($maybeUrlOrPath);
    }

    // URL publik untuk OG/Twitter image (ambil dari dokumen pertama)
    public function getThumbnailUrlAttribute(): ?string
    {
        $first = collect($this->documents)->first();
        if (!$first) return null;

        // Jika resolveArrayRelations mengembalikan array (hasil resource-like)
        if (is_array($first)) {
            // 1) langsung pakai file_url bila ada
            if (!empty($first['file_url'])) {
                return $first['file_url'];
            }
            // 2) normalisasi file_path/url/path
            $candidate = $first['file_path'] ?? $first['url'] ?? $first['path'] ?? null;
            return $this->normalizeDocUrl($candidate);
        }

        // Jika berupa model Document
        $url = $first->file_url ?? null;       // accessor di Document (jika ada)
        $path = $first->file_path ?? $first->path ?? null;
        return $url ?: $this->normalizeDocUrl($path);
    }
}
