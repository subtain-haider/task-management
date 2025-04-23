<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllTasks($userId, $filters)
    {
        $query = Task::where('user_id', $userId)->latest();
        
        // Filter by status
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Filter by due date
        if (isset($filters['due_date'])) {
            $query->whereDate('due_date', $filters['due_date']);
        }
        
        // Filter by date range
        if (isset($filters['from_date']) && isset($filters['to_date'])) {
            $query->whereBetween('due_date', [$filters['from_date'], $filters['to_date']]);
        }
        
        // Search
        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        return $query->paginate($filters['per_page'] ?? config('app.pagination.per_page'));
    }
    
    public function getTaskById($id)
    {
        return Task::findOrFail($id);
    }
    
    public function createTask(array $data)
    {
        return Task::create($data);
    }
    
    public function updateTask($id, array $data)
    {
        $task = Task::findOrFail($id);
        $task->update($data);
        return $task;
    }
    
    public function deleteTask($id)
    {
        $task = Task::findOrFail($id);
        return $task->delete();
    }
}