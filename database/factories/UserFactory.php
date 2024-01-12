<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();
        $role = fake()->randomElement([2, 3]);
        
        return [
            'nama' => $firstName . ' ' . $lastName,
            'speciality' => fake()->word().' '.fake()->word(),
            'role' => $role,
            'email' => strtolower($firstName.$lastName).random_int(1,100).'@big.com',
            'email_verified_at' => now(),
            'password' => 'password',
            'wilayah_id' => function () {
                return \App\Models\Wilayah::inRandomOrder()->first()->id;
            },
            'no_telp' => fake()->numerify('628##########'),
            'foto_profil' => 'profile/dummy.png',
            'poin' => 0,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
