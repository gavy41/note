<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'openid'        => 'fake_' . $this->faker->unique()->uuid,
            'nickname'      => $this->faker->name,
            'last_login_at' => now(),
        ];
    }
}
