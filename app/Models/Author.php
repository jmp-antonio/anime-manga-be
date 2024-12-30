<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{

    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
    ];

    /* 
        Custom attributes
    */

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    protected $appends = ['full_name'];

    /* 
        Scopes
    */
    public function scopeFilter($query, $name)
    {
        if ($name) {
            $query->where(function ($query) use ($name) {
                $query->where('first_name', 'like', "%{$name}%")
                    ->orWhere('last_name', 'like', "%{$name}%");
            });
        }
    }
}
