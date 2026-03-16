<?php

namespace Database\Factories;

use App\Models\Card;
use Illuminate\Database\Eloquent\Factories\Factory;

class CardFactory extends Factory
{
    protected $model = Card::class;

    public function definition()
    {
        $colors = ['#e8e2d8','#dde8e2','#e2dde8','#e8e2dd','#dde2e8',
                   '#e8e5d8','#e2e8dd','#e8dde5','#d8e2e8','#e5e8d8'];
        return [
            'type'    => $this->faker->randomElement(['excerpt','inspiration','quote']),
            'content' => $this->faker->sentence(12),
            'source'  => $this->faker->optional()->word,
            'author'  => $this->faker->optional()->name,
            'color'   => $this->faker->randomElement($colors),
        ];
    }
}
