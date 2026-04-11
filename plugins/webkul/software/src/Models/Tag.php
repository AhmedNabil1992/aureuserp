<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'software_tags';

    protected $fillable = [
        'name',
        'color',
    ];

    public function tickets(): BelongsToMany
    {
        return $this->belongsToMany(Ticket::class, 'software_ticket_tag', 'tag_id', 'ticket_id')
            ->withTimestamps();
    }
}
