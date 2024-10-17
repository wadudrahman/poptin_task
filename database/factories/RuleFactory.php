<?php

namespace Database\Factories;

use App\Models\Rule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Ramsey\Uuid\Uuid;

class RuleFactory extends Factory
{
    protected $model = Rule::class;

    public function definition(): array
    {
        return [
            'uuid' => Uuid::uuid4()->toString(),
            'action' => 'show',
            'condition' => 'contains',
            'url' => $this->faker->slug,
            'user_id' => null
        ];
    }
}
