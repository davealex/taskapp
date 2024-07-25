<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    const LOGIN_PATH = '/api/auth/login';

    /**
     * Test that user can login using valid credentials.
     *
     * @return void
     */
    public function test_that_user_can_login_with_valid_credentials()
    {
        $user = UserFactory::new()->create();

        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->postJson(self::LOGIN_PATH, [
                'email' => $user->email,
                'password' => env('VALID_PASSWORD_SAMPLE')
            ]);

        $response->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll(['message', 'data', 'data.token'])
            );
    }

    /**
     * Test that user cannot login using invalid credentials.
     *
     * @return void
     */
    public function test_that_user_cannot_login_without_valid_credentials()
    {
        $user = UserFactory::new()->create();

        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->postJson(self::LOGIN_PATH, [
                'email' => $user->email,
                'password' => Str::random()
            ]);

        $response->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll(['message', 'errors'])
            );
    }
}
