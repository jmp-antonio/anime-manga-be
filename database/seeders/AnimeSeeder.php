<?php

namespace Database\Seeders;

use App\Models\Anime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Anime::create(['title' => 'One Piece', 'author_id' => 1]);
        Anime::create(['title' => 'Black Clover', 'author_id' => 2]);
        Anime::create(['title' => 'One Punch Man', 'author_id' => 3]);
        Anime::create(['title' => 'Blue Lock', 'author_id' => 4]);
        Anime::create(['title' => 'Kaiju No. 8', 'author_id' => 5]);
        Anime::create(['title' => 'Demon Slayer', 'author_id' => 6]);
    }
}
