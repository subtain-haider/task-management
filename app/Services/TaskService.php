<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TaskService
{
    protected $taskRepository;
    
    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }
    
    public function getAllTasks($filters = [])
    {
        return $this->taskRepository->getAllTasks(Auth::id(), $filters);
    }
    
    public function getTaskById($id)
    {
        $task = $this->taskRepository->getTaskById($id);
        
        if (Gate::denies('view', $task)) {
            throw new AuthorizationException('This action is unauthorized.');
        }
        
        return $task;
    }
    
    public function createTask(array $data)
    {
        $data['user_id'] = Auth::id();
        return $this->taskRepository->createTask($data);
    }
    
    public function updateTask($id, array $data)
    {
        $task = $this->taskRepository->getTaskById($id);
        
        if (Gate::denies('update', $task)) {
            throw new AuthorizationException('This action is unauthorized.');
        }
        
        $this->taskRepository->updateTask($id, $data);
        return $task->fresh();
    }
    
    public function deleteTask($id)
    {
        $task = $this->taskRepository->getTaskById($id);
        
        if (Gate::denies('delete', $task)) {
            throw new AuthorizationException('This action is unauthorized.');
        }
        
        return $this->taskRepository->deleteTask($id);
    }
}