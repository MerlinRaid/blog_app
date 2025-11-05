<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word(); //Genereerib unikaalse sõna
        return [
            'name' => ucfirst($name), //Ucfirst sõna esimene täht suureks
            'slug' => Str::slug($name), //Teisendab nime URL-sõbralikuks vorminguks
        ];
    }
}
