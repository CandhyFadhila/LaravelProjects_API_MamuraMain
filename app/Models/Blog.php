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
        if (Str::startsWith($maybeUrlOrPath, ['http://', 'https://'])) return $maybeUrlOrPath;

        // Base URL dokumen (server file terpisah)
        $base = rtrim(env('DOMAIN_STORAGE'), '/');
        if ($base !== '') {
            return $base . '/' . ltrim($maybeUrlOrPath, '/');
        }
        // Fallback kalau memang satu server & pakai disk public
        return Storage::url($maybeUrlOrPath);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        $first = collect($this->documents)->first();
        if (!$first) return null;

        if (is_array($first)) {
            // 1) langsung gunakan file_url jika ada
            if (!empty($first['file_url'])) return $first['file_url'];

            // 2) jika tidak ada, pakai file_path/url/path → normalisasi
            $candidate = $first['file_path'] ?? $first['url'] ?? $first['path'] ?? null;
            return $this->normalizeDocUrl($candidate);
        }

        // Jika $first adalah model Document
        $url  = $first->file_url ?? null;
        $path = $first->file_path ?? $first->path ?? null;
        return $url ?: $this->normalizeDocUrl($path);
    }
}
