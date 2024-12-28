<?php

namespace Database\Factories;

use App\Models\MangaLink;
use App\Models\Anime;
use Illuminate\Database\Eloquent\Factories\Factory;

class MangaLinkFactory extends Factory
{
    protected $model = MangaLink::class;

    public function definition()
    {
        return [
            'url' => $this->faker->url,
            'anime_id' => Anime::factory(),
        ];
    }
}
