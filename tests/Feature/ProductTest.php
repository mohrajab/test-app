<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        User::factory()->create();
        Auth::setUser(User::first());
        $this->faker = Factory::create();
        Storage::fake();
    }

    public function test_user_can_add_new_product()
    {
        $this->assertEquals(0, Product::count());
        $productData = Product::factory()->make();
        $response = $this->post('/api/products', $productData->toArray(), ['accept' => 'application/json']);

        $response->assertStatus(201);
        $this->assertEquals(1, Product::count());
    }

    public function test_user_cant_add_new_product_if_parameter_is_missed()
    {
        $this->assertEquals(0, Product::count());
        $response = $this->post('/api/products', [], ['accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name',
            'manufactured_year',
            'photo'
        ]);
        $this->assertEquals(0, Product::count());
    }

    public function test_user_cant_add_new_product_if_manufactured_year_less_than_1990()
    {
        $this->assertEquals(0, Product::count());
        $productData = Product::factory()->make()->toArray();
        $productData['manufactured_year'] = 1989;
        $response = $this->post('/api/products', $productData, ['accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'manufactured_year',
        ]);
        $this->assertEquals(0, Product::count());
    }

    public function test_user_cant_add_new_product_if_manufactured_year_greater_than_present()
    {
        $this->assertEquals(0, Product::count());
        $productData = Product::factory()->make()->toArray();
        $productData['manufactured_year'] = now()->addYear()->year;
        $response = $this->post('/api/products', $productData, ['accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'manufactured_year',
        ]);
        $this->assertEquals(0, Product::count());
    }

    public function test_user_cant_add_new_product_if_photo_not_image()
    {
        $this->assertEquals(0, Product::count());
        $productData = Product::factory()->make()->toArray();
        $productData['photo'] = UploadedFile::fake()->create('test.pdf');
        $response = $this->post('/api/products', $productData, ['accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'photo',
        ]);
        $this->assertEquals(0, Product::count());
    }

    public function test_user_can_edit_product()
    {
        $product = Product::factory()->withUser()->create();
        $this->assertEquals(1, Product::count());
        $productData = Product::factory()->make();
        $response = $this->put('/api/products/' . $product->id, $productData->toArray(), ['accept' => 'application/json']);

        $response->assertStatus(200);
        $this->assertEquals(1, Product::count());
    }

    public function test_user_cant_edit_product_if_it_is_not_belong_to_him()
    {
        $user2 = User::factory()->create();
        $product = Product::factory()->withUser($user2->id)->create();
        $this->assertEquals(1, Product::count());
        $productData = Product::factory()->make();
        $response = $this->put('/api/products/' . $product->id, $productData->toArray(), ['accept' => 'application/json']);

        $response->assertStatus(403);
    }

    public function test_user_can_edit_product_without_uploading_new_photo()
    {
        $product = Product::factory()->withUser()->create();
        $this->assertEquals(1, Product::count());
        $response = $this->put('/api/products/' . $product->id, [
            'name' => $this->faker->domainName,
            'manufactured_year' => 1990
        ], ['accept' => 'application/json']);

        $response->assertStatus(200);
        $this->assertEquals(1, Product::count());
    }

    public function test_user_can_list_products()
    {
        // step 1
        Product::factory(4)->withUser()->create();
        $response = $this->get('/api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(4, 'data');

        // step 2
        $user = User::factory()->create();
        Product::factory(3)->withUser($user->id)->create();
        $response = $this->get('/api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(4, 'data'); // not (7) because the new created products are not belong to the current user

        // step 3
        Product::factory(10)->withUser()->create();
        $response = $this->get('/api/products');
        $response->assertStatus(200);
        $response->assertJsonCount(10, 'data'); // not (14), because we fetch only 10 per page
    }

    public function test_user_can_get_product_details()
    {
        $product = Product::factory()->withUser()->create();
        $response = $this->get('/api/products/' . $product->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'manufactured_year',
                'photo'
            ]
        ]);
    }

    public function test_user_cant_get_product_details_if_product_is_not_belong_to_him()
    {
        $user = User::factory()->create();
        $product = Product::factory()->withUser($user->id)->create();
        $response = $this->get('/api/products/' . $product->id);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_product()
    {
        $product = Product::factory()->withUser()->create();
        $this->assertEquals(1, Product::count());
        $response = $this->delete('/api/products/' . $product->id);

        $response->assertStatus(200);
        $this->assertEquals(0, Product::count());
    }

    public function test_user_cant_delete_product_if_product_is_not_belong_to_him()
    {
        $user = User::factory()->create();
        $product = Product::factory()->withUser($user->id)->create();
        $this->assertEquals(1, Product::count());
        $response = $this->delete('/api/products/' . $product->id);

        $response->assertStatus(403);
        $this->assertEquals(1, Product::count());
    }
}
