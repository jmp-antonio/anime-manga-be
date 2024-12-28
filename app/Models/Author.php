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
