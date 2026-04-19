<?php

namespace Webkul\Article\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Security\Models\User;

class Tag extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'articles_tags';

    protected $fillable = [
        'name',
        'color',
        'sort',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class, 'articles_article_tags', 'tag_id', 'article_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($tag) {
            $tag->creator_id ??= Auth::id();
        });
    }
}
