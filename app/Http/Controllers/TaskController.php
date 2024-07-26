<?php

namespace App\Http\Controllers;

use App\Dtos\TaskDto;
use App\Enums\TaskOrderByEnum;
use App\Enums\TaskPriorityEnum;
use App\Enums\TaskStatusEnum;
use App\Exceptions\NotTaskAuthorException;
use App\Http\Requests\ListTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\TaskService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ListTaskRequest $request, TaskService $service): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => 'Task List',
            'data' => [
                'tasks' => $service->index(TaskDto::fromRequest($request)),
                'options' => [
                    'status' => TaskService::formatListingOptions(TaskStatusEnum::values()),
                    'priority' => TaskService::formatListingOptions(TaskPriorityEnum::values()),
                    'order_by' => TaskService::formatListingOptions(TaskOrderByEnum::values())
                ]
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => 'Task creation Options',
            'data' => [
                'options' => [
                    'status' => TaskService::formatListingOptions(TaskStatusEnum::values()),
                    'priority' => TaskService::formatListingOptions(TaskPriorityEnum::values()),
                ]
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, TaskService $service): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json([
                'message' => 'Task create',
                'data' => [
                    'task' => $service->create(TaskDto::fromRequest($request))
                ],
            ], 201);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'message' => 'An error occurred while processing your request. Please try again shortly',
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $ref, TaskService $service): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json([
                'message' => 'Fetch Task',
                'data' => [
                    'task' => $service->getByRef($ref),
                ],
            ]);
        } catch (ModelNotFoundException|NotFoundHttpException $exception) {
            return response()->json([
                'message' => 'Task not found!',
            ], 404);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'message' => 'An error occurred while processing your request. Please try again shortly',
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $ref, TaskService $service): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'message' => 'Task editing Options',
            'data' => [
                'task' => $service->getByRef($ref),
                'options' => [
                    'status' => TaskService::formatListingOptions(TaskStatusEnum::values()),
                    'priority' => TaskService::formatListingOptions(TaskPriorityEnum::values()),
                ]
            ],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, string $ref, TaskService $service): \Illuminate\Http\JsonResponse
    {
        try {
            return response()->json([
                'message' => 'Task updated',
                'data' => [
                    'task' => $service->update($ref, TaskDto::fromRequest($request)),
                ],
            ]);
        } catch (ModelNotFoundException|NotFoundHttpException $exception) {
            return response()->json([
                'message' => 'Task not found!',
            ], 404);
        } catch (NotTaskAuthorException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'message' => 'An error occurred while processing your request. Please try again shortly',
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $ref, TaskService $service): \Illuminate\Http\JsonResponse
    {
        try {
            $service->delete($ref);

            return response()->json([
                'message' => 'Task deleted'
            ]);
        } catch (ModelNotFoundException|NotFoundHttpException $exception) {
            return response()->json([
                'message' => 'Task not found!',
            ], 404);
        } catch (NotTaskAuthorException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->getCode());
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return response()->json([
                'message' => 'An error occurred while processing your request. Please try again shortly',
            ], 500);
        }
    }
}
