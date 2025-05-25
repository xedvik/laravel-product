<?php

namespace Tests\Feature\Auth;


use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
class AuthApiTest extends TestCase
{
    use DatabaseTransactions;

    protected array $validData;

    protected function setUp(): void
    {
        parent::setUp();
        // Стандартные корректные данные
        $this->validData = [
            'name' => 'test name',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ];
    }

    public function user_can_register_successfully()
    {
        $response = $this->postJson('/api/register', $this->validData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data',
                'message',

            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function registration_fails_if_password_confirmation_does_not_match()
    {
        $invalidData = $this->validData;
        $invalidData['password_confirmation'] = 'WrongPassword';

        $response = $this->postJson('/api/register', $invalidData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
    }

    public function registration_fails_if_email_already_taken()
    {
        User::factory()->create([
            'name' => 'test name',
            'email' => 'test@example.com',
            'password' => 'Password1232',
        ]);

        $response = $this->postJson('/api/register', $this->validData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_user_can_login_and_receive_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->postJson(
            '/api/login',
            [
                'email' => 'test@example.com',
                'password' => '12345678',
            ]
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'message',
        ]);
        $this->assertArrayHasKey('token', $response->json('data'));
    }
    public function test_login_fails_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('12345678'),
        ]);

        $response = $this->postJson(
            '/api/login',
            [
                'email' => 'test@example.com',
                'password' => 'wrong_12345678',
            ]
        );

        $response->assertStatus(401);
        $response->assertJsonStructure([
            'message',
        ]);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }
}
