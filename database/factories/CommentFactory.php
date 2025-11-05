<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::inRandomOrder()->value('id'), //Võtab juhusliku postituse ID
            'user_id' => User::inRandomOrder()->value('id'), //Võtab juhusliku kasutaja ID
            'body' => $this->faker->paragraph(), //Genereerib juhusliku lõigu 
            'status' => $this->faker->randomElement(['pending', 'approved']), //Valib juhuslikult staatuse
            'ip_address' => $this->faker->ipv4(), //Genereerib juhusliku IPv4 aadressi
        ];
    }
}
