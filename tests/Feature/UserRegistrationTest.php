<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;
    const REGISTER_PATH = '/api/auth/register';

    /**
     * Test that user can be created with valid details.
     *
     * @return void
     */
    public function test_that_user_can_register_with_valid_credentials()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->postJson(self::REGISTER_PATH, [
                'first_name' => 'Jack',
                'last_name' => 'Sparrow',
                'email' => 'jacksparrow@email.com',
                'password' => \Illuminate\Support\Facades\Hash::make(env('VALID_PASSWORD_SAMPLE'))
            ]);

        $response->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll(['message', 'data', 'data.token'])
            );
    }

    /**
     * Test that user cannot register without a valid email.
     *
     * @return void
     */
    public function test_that_user_cannot_register_without_valid_email()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->postJson(self::REGISTER_PATH, [
                'first_name' => 'Jack',
                'last_name' => 'Sparrow',
                'email' => 'jacksparrow.com',
                'password' => \Illuminate\Support\Facades\Hash::make(env('VALID_PASSWORD_SAMPLE'))
            ]);

        $response->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll(['message', 'errors.email'])
            );
    }

    /**
     * Test that user cannot register without a first_name.
     *
     * @return void
     */
    public function test_that_user_cannot_register_without_first_name()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->postJson(self::REGISTER_PATH, [
                'first_name' => '',
                'last_name' => 'King',
                'email' => 'sallyking@email.com',
                'password' => \Illuminate\Support\Facades\Hash::make(env('VALID_PASSWORD_SAMPLE'))
            ]);

        $response->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll(['message', 'errors.first_name'])
            );
    }

    /**
     * Test that user cannot register without a last_name.
     *
     * @return void
     */
    public function test_that_user_cannot_register_without_last_name()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->postJson(self::REGISTER_PATH, [
                'first_name' => 'Steve',
                'last_name' => '',
                'email' => 'sallyking@email.com',
                'password' => \Illuminate\Support\Facades\Hash::make(env('VALID_PASSWORD_SAMPLE'))
            ]);

        $response->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll(['message', 'errors.last_name'])
            );
    }

    /**
     * Test that user cannot register without a valid password.
     *
     * @return void
     */
    public function test_that_user_cannot_register_without_valid_password()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->postJson(self::REGISTER_PATH, [
                'first_name' => 'Regina',
                'last_name' => 'King',
                'email' => 'regina.king@email.com',
                'password' => 'password'
            ]);

        $response->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll(['message', 'errors.password'])
            );
    }
}
