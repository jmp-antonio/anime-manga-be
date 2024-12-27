<?php

namespace Database\Factories;

use App\Models\Anime;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnimeFactory extends Factory
{
    protected $model = Anime::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'author_id' => \App\Models\Author::factory(), // Assuming you have an Author model and factory
        ];
    }
}
