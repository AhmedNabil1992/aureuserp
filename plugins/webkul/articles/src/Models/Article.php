<?php

namespace Webkul\Article\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\Security\Models\User;
use Webkul\Software\Models\Program;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'articles_articles';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'cover_image',
        'video_embed_url',
        'files',
        'is_internal',
        'is_published',
        'published_at',
        'sort',
        'category_id',
        'author_id',
        'creator_id',
        'last_editor_id',
    ];

    protected function casts(): array
    {
        return [
            'files'        => 'array',
            'is_internal'  => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        return Storage::url($this->cover_image);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'articles_article_tags', 'article_id', 'tag_id');
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'articles_article_programs', 'article_id', 'program_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function lastEditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_editor_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($article) {
            $article->creator_id ??= Auth::id();
            $article->author_id ??= Auth::id();
        });

        static::updating(function ($article) {
            $article->last_editor_id = Auth::id();
        });
    }
}
