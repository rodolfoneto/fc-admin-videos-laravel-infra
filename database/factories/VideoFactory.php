<?php

namespace Database\Factories;

use Core\Domain\Enum\Rating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ratingArray = Rating::cases();
        return [
            'id' => fake()->uuid(),
            'title' => fake()->name(),
            'description' => fake()->text(),
            'year_launched' => fake()->numberBetween(2000, 2023),
            'opened' => fake()->boolean(50),
            'rating' => $ratingArray[array_rand($ratingArray)],
            'duration' => fake()->numberBetween(10, 3000),
            'created_at' => now(),
        ];
    }
}
