<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
final class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'unit_id' => fake()->randomNumber(),
            'api_key' => fake()->uuid(),
        ];
    }
}
