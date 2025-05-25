<?php

namespace Tests\Feature\Auth;


use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
class ProductApiTest extends TestCase
{
    use DatabaseTransactions;

    protected array $validData;
    protected object $user;
    protected object $admin;
    protected object $product;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'user',
        ]);
        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);
        $this->product =  Product::factory()->create([
            'name' => 'Test Product',
            'description' => 'test descript',
            'purchase_price' => 500,
            'rent_price_per_hour' => 100,
        ]);
        // Стандартные корректные данные
        $this->validData = [
            'name' => 'test tovar',
            'description' => 'opicanie',
            'purchase_price' => '300',
            'rent_price_per_hour' => '200',
        ];
    }

    public function test_product_create_success()
    {
        $token = $this->admin->createToken('TestToken')->plainTextToken;
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->postJson('/api/products/', $this->validData);
        $response->assertStatus(201)->assertJsonStructure([
            'data',
            'message'
        ]);
    }
    public function test_non_admin_cannot_create_product()
    {
        Product::query()->delete();
        $token = $this->user->createToken('TestToken')->plainTextToken;
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->postJson('/api/products/', $this->validData);
        $response->assertStatus(403)->assertJsonStructure([
            'message'
        ]);

    }
    public function test_product_update_success()
    {
        $token = $this->admin->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->putJson('/api/products/'.$this->product->id, $this->validData);
        $response->assertStatus(200)->assertJsonStructure([
            'data',
            'message'
        ]);
    }
    public function test_non_admin_cannot_update_product()
    {
        $token = $this->user->createToken('TestToken')->plainTextToken;
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->putJson('/api/products/'.$this->product->id, $this->validData);
        $response->assertStatus(403)->assertJsonStructure([
            'message'
        ]);

    }
    public function test_product_delete_success()
    {
        $token = $this->admin->createToken('TestToken')->plainTextToken;
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->deleteJson('/api/products/'.$this->product->id);
        $response->assertStatus(200)->assertJsonStructure([
            'message'
        ]);

    }
    public function test_non_admin_cannot_delete_product()
    {
        $token = $this->user->createToken('TestToken')->plainTextToken;
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->deleteJson('/api/products/'.$this->product->id);
        $response->assertStatus(403)->assertJsonStructure([
            'message'
        ]);
    }
    public function test_success_get_products()
    {
        $token = $this->user->createToken('TestToken')->plainTextToken;
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->getJson('/api/products/');
        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
            '*' => [
                'id','name','description','purchase_price','rent_price_per_hour','created_at'
            ]],
            'message'
        ]);
    }
    public function test_unsuccess_get_products(){
        $token = $this->user->createToken('TestToken')->plainTextToken;
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->getJson('/api/products/');
        $response->assertStatus(200)->assertJsonStructure([
            'data',
            'message'
        ]);
        $response->assertJson([
            'data' => []
        ]);
    }

    public function test_success_get_product(){
        $token = $this->user->createToken('TestToken')->plainTextToken;
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->getJson('/api/products/'.$this->product->id);
        $response->assertStatus(200)->assertJsonStructure([
            'data' => [
                'id','name','description','purchase_price','rent_price_per_hour','created_at'
            ],
            'message'
        ]);
    }
    public function test_unsuccess_get_product(){
        $token = $this->user->createToken('TestToken')->plainTextToken;
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token,])->getJson('/api/products/55');
        $response->assertStatus(404)->assertJsonStructure([
            'message'
        ]);
    }


}