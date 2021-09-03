<?php

namespace Database\Factories;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->domainName,
            'manufactured_year' => $this->faker->numberBetween(1990, Carbon::now()->year),
            'photo' => UploadedFile::fake()->image('photo1.jpg')
        ];
    }

    public function withUser($user_id = 1)
    {
        return $this->afterMaking(function (Product $product) use ($user_id) {
            $product->user_id = $user_id;
        });
    }
}
