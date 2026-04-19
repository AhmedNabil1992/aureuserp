<?php

namespace Webkul\Article\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Webkul\Security\Models\User;

class Category extends Model implements Sortable
{
    use HasFactory, SoftDeletes, SortableTrait;

    protected $table = 'articles_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'sort',
        'creator_id',
    ];

    public $sortable = [
        'order_column_name'  => 'sort',
        'sort_when_creating' => true,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'category_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($category) {
            $category->creator_id ??= Auth::id();
        });
    }
}
