<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word(2, true);  //Genereerib unikaalse kahe-sõnalise sõna
        return [
            'name' => ucfirst($name), //Ucfirst iga sõna esimene täht suureks
            'slug' => Str::slug($name), //Teisendab nime URL-sõbralikuks vorminguks
            'description' => $this->faker->optional()->sentence(10), //Genereerib juhusliku lause kirjelduseks
        ];
    }
}
