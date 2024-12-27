<?php

namespace Database\Seeders;

use App\Models\MangaLink;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MangaLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MangaLink::create(['url' => 'https://www.viz.com/shonenjump/chapters/one-piece', 'anime_id' => 1]);
        MangaLink::create(['url' => 'https://www.viz.com/shonenjump/chapters/black-clover', 'anime_id' => 2]);
        MangaLink::create(['url' => 'https://www.viz.com/shonenjump/chapters/one-punch-man', 'anime_id' => 3]);
        MangaLink::create(['url' => 'https://ww1.readbluelock.com/', 'anime_id' => 4]);
        MangaLink::create(['url' => 'https://www.viz.com/shonenjump/chapters/kaiju-no-8', 'anime_id' => 5]);
        MangaLink::create(['url' => 'https://www.viz.com/shonenjump/chapters/demon-slayer-kimetsu-no-yaiba', 'anime_id' => 6]);
    }
}
