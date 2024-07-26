<?php

namespace Tests\Feature;

use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;
    const TASKS_PATH = '/api/tasks';

    /**
     * Test that task listing API returns data.
     *
     * @return void
     */
    public function test_that_tasks_paginated_list_can_be_retrieved()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->withToken($this->getAuthUserToken())
            ->getJson(self::TASKS_PATH);

        $response->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll(['message', 'data', 'data.tasks.current_page'])
            );
    }

    /**
     * Test that a task can be created with valid details.
     *
     * @return void
     */
    public function test_that_user_can_create_a_task_with_valid_parameters()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->withToken($this->getAuthUserToken())
            ->postJson(self::TASKS_PATH.'/store', [
                'title' => 'Sample 1',
                'description' => 'sample 2 description',
                'priority' => TaskPriorityEnum::Low->value,
                'status' => TaskStatusEnum::Pending->value
            ]);

        $response->assertStatus(201)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll(['message', 'data', 'data.task'])
            );
    }

    /**
     * Test that a task cannot be created without a title.
     *
     * @return void
     */
    public function test_that_user_cannot_create_a_task_without_a_title()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->withToken($this->getAuthUserToken())
            ->postJson(self::TASKS_PATH.'/store', [
                'description' => 'sample 3 description',
                'priority' => TaskPriorityEnum::Low->value,
                'status' => TaskStatusEnum::Pending->value
            ]);

        $response->assertStatus(422)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll(['message', 'errors.title'])
            );
    }

    /**
     * Test that authentication is required to create a new task.
     *
     * @return void
     */
    public function test_that_only_authenticated_user_can_create_a_task()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->postJson(self::TASKS_PATH.'/store', [
                'title' => 'Sample 1',
                'description' => 'sample 2 description',
                'priority' => TaskPriorityEnum::Low->value,
                'status' => TaskStatusEnum::Pending->value
            ]);

        $response->assertStatus(401);
    }

    /**
     * Test that an existing task can be retrieved for viewing.
     *
     * @return void
     */
    public function test_that_an_existing_task_can_be_viewed()
    {
        $taskRef = $this->withHeaders(['Accept' => 'application/json'])
            ->withToken($this->getAuthUserToken())
            ->postJson(self::TASKS_PATH.'/store', [
                'title' => 'Sample 3',
                'description' => 'sample 3 description',
                'priority' => TaskPriorityEnum::Low->value,
                'status' => TaskStatusEnum::Pending->value
            ])['data']['task']['ref'];

        $this->withHeaders(['Accept' => 'application/json'])
            ->withToken($this->getAuthUserToken())
            ->getJson(self::TASKS_PATH."/$taskRef")
            ->assertStatus(200);
    }

    /**
     * Test that an existing task can be updated.
     *
     * @return void
     */
    public function test_that_an_existing_task_can_be_updated()
    {
        $taskRef = $this->withHeaders(['Accept' => 'application/json'])
            ->withToken($this->getAuthUserToken())
            ->postJson(self::TASKS_PATH.'/store', [
                'title' => 'Sample 3',
                'description' => 'sample 3 description',
                'priority' => TaskPriorityEnum::Low->value,
                'status' => TaskStatusEnum::Pending->value
            ])['data']['task']['ref'];

        $this->withHeaders(['Accept' => 'application/json'])
            ->withToken($this->getAuthUserToken())
            ->patchJson(self::TASKS_PATH."/$taskRef/update", [
                'priority' => $newValue = TaskPriorityEnum::High->value
            ])
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->hasAll(['message', 'data.task.priority'])
            )->assertJsonPath('data.task.priority', $newValue);
    }

    /**
     * Test that an existing task can be deleted.
     *
     * @return void
     */
    public function test_that_an_existing_task_can_be_deleted()
    {
        $taskRef = $this->withHeaders(['Accept' => 'application/json'])
            ->withToken($this->getAuthUserToken())
            ->postJson(self::TASKS_PATH.'/store', [
                'title' => 'Sample 3',
                'description' => 'sample 3 description',
                'priority' => TaskPriorityEnum::Low->value,
                'status' => TaskStatusEnum::Pending->value
            ])['data']['task']['ref'];

        $this->withHeaders(['Accept' => 'application/json'])
            ->withToken($this->getAuthUserToken())
            ->deleteJson(self::TASKS_PATH."/$taskRef/delete")
            ->assertStatus(200);
    }

    /**
     * @return string
     */
    private function getAuthUserToken(): string
    {
        return $this->withHeaders(['Accept' => 'application/json'])
            ->postJson(UserLoginTest::LOGIN_PATH, [
                'email' => UserFactory::new()->create()?->email,
                'password' => env('VALID_PASSWORD_SAMPLE')
            ])['data']['token'];
    }
}
