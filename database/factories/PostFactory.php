<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(); //Genereerib unikaalse lause pealkirjaks
        $status = $this->faker->randomElement(['draft','review', 'published']); //Valib juhuslikult staatuse
        return [
            'user_id' => User::inRandomOrder()->value('id'), //Võtab juhusliku kasutaja ID
            'category_id' => $this->faker->boolean(70) ? Category::inRandomOrder()->value('id') : null, //70% tõenäosusega määrab juhusliku kategooria ID või null
            'title' => $title, //Määrab pealkirja
            'slug' => Str::slug($title).'-'. Str::random(5), //Teisendab pealkirja URL-sõbralikuks vorminguks ja lisab juhusliku 5-tähelise stringi
            'excerpt' => $this->faker->optional()->paragraph(), //Genereerib lühikese lõigu kokkuvõtteks
            'body' => $this->faker->paragraphs(6, true), //Genereerib 6 lõiku postituse sisuks
            'status' => $status, //Määrab staatuse
            'published_at' => $status === 'published' ? $this->faker->dateTimeBetween('-30 days', 'now') : null, //Kui staatuseks on 'published', määrab juhusliku kuupäeva viimase 30 päeva jooksul, muidu null
            'featured_image' => null, //Pilti ei kasutata
            'reading_time' => $this->faker->numberBetween(2, 10), //Genereerib juhusliku lugemisaja 2 kuni 10 minuti vahel
        ];
    }
}
