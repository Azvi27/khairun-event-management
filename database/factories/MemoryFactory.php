<?php

namespace Database\Factories;

use App\Models\Memory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MemoryFactory extends Factory
{
    protected $model = Memory::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'memory_date' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'description' => $this->faker->paragraph(2),
            'image_path' => $this->faker->optional(0.7)->randomElement([
                'memories/sample1.jpg',
                'memories/sample2.png',
                'memories/sample3.gif'
            ]),
            'spotify_track_id' => $this->faker->optional(0.5)->regexify('spotify:track:[a-zA-Z0-9]{22}')
        ];
    }

    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_path' => 'memories/test_image.jpg'
        ]);
    }

    public function withoutImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image_path' => null
        ]);
    }

    public function withSpotify(): static
    {
        return $this->state(fn (array $attributes) => [
            'spotify_track_id' => 'spotify:track:4iV5W9uYEdYUVa79Axb7Rh'
        ]);
    }
}