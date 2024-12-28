<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MangaLink extends Model
{
    use HasFactory;

    protected $fillable = ['url', 'anime_id'];
}
