<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anime extends Model
{

    use HasFactory;

    protected $fillable = [
        'title',
        'author_id',
    ];

    protected $with = ['author', 'mangaLinks'];

    /* 
        Scopes
    */
    public function scopeFilter($query, $title, $author, $sortBy = 'id', $sortDirection = 'asc')
    {
        return $query->when($title, function ($query, $title) {
            return $query->where('title', 'like', '%' . $title . '%');
        })->when($author, function ($query, $author) {
            return $query->whereHas('author', function ($query) use ($author) {
                $query->where('first_name', 'like', '%' . $author . '%')
                    ->orWhere('last_name', 'like', '%' . $author . '%');
            });
        })->join('authors', 'animes.author_id', '=', 'authors.id') // Join authors table
            ->orderBy($sortBy === 'author' ? 'authors.first_name' : $sortBy, $sortDirection) // Sort by first_name if specified
            ->select('animes.*');
    }

    /* 
        Relationships
    */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function mangaLinks(): HasMany
    {
        return $this->hasMany(MangaLink::class);
    }
}
