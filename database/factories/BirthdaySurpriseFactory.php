<?php

namespace Database\Factories;

use App\Models\BirthdaySurprise;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class BirthdaySurpriseFactory extends Factory
{
    protected $model = BirthdaySurprise::class;

    public function definition(): array
    {
        return [
            'sender_user_id' => User::factory(),
            'receiver_user_id' => User::factory(),
            'content_type' => $this->faker->randomElement(['message', 'image', 'video_link']),
            'content_payload' => $this->faker->sentence(10),
            'reveal_at' => $this->faker->dateTimeBetween('now', '+30 days'),
            'is_revealed' => false,
            'content' => null,
        ];
    }

    /**
     * State untuk surprise yang sudah terbuka
     */
    public function revealed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_revealed' => true,
            'reveal_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * State untuk surprise yang bisa dibuka hari ini
     */
    public function canBeRevealed(): static
    {
        return $this->state(fn (array $attributes) => [
            'reveal_at' => now()->subHours(1),
            'is_revealed' => false,
        ]);
    }

    /**
     * State untuk surprise masa depan
     */
    public function future(): static
    {
        return $this->state(fn (array $attributes) => [
            'reveal_at' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'is_revealed' => false,
        ]);
    }

    /**
     * State untuk surprise dengan gambar
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => 'image',
            'content' => 'birthday_surprises/test-image.jpg',
        ]);
    }
}
