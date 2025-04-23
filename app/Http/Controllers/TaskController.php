<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    protected $taskService;
    
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters = $request->only(['status', 'due_date', 'from_date', 'to_date', 'search', 'per_page']);
        $tasks = $this->taskService->getAllTasks($filters);
        
        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): TaskResource
    {
        $task = $this->taskService->createTask($request->validated());
        
        return new TaskResource($task);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): TaskResource
    {
        $task = $this->taskService->getTaskById($id);
        
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, $id): TaskResource
    {
        $task = $this->taskService->updateTask($id, $request->validated());
        
        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): Response
    {
        $this->taskService->deleteTask($id);
        
        return response()->noContent();
    }
}