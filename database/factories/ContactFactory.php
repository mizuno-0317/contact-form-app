<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id'=> Category::inRandomOrder()->first()->id,
            'first_name'=>fake()->firstName(),
            'last_name'=>fake()->lastName(),
            'gender'=>fake()->randomElement([1,2,3]),
            'email'=>fake()->email,
            'tel'=>fake()->randomElement([
                fake()->numerify('080########'),
                fake()->numerify('0120#######'),
            ]),
            'address'=>fake()->address,
            'building'=>fake()->sentence,
            'detail'=>fake()->sentence,
        ];
    }
}
