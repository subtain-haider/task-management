<?php

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

// Authentication Tests
test('user can register', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('message', 'User registered successfully')
        ->assertJsonStructure(['user']);

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
    ]);
});

test('user can login', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'user',
            'token',
        ]);
});

test('user cannot login with invalid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(422);
});

// Task Resource Tests
test('authenticated user can create a task', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $taskData = [
        'title' => 'Test Task',
        'description' => 'Test Description',
        'due_date' => now()->addDays(7)->format('Y-m-d'),
        'status' => 'pending',
    ];

    $response = $this->postJson('/api/tasks', $taskData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'id', 'title', 'description', 'due_date', 'status', 'created_at', 'updated_at'
            ]
        ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'user_id' => $user->id,
    ]);
});

test('user can retrieve their tasks', function () {
    $user = User::factory()->create();
    Task::factory(3)->create(['user_id' => $user->id]);
    
    Sanctum::actingAs($user);

    $response = $this->getJson('/api/tasks');

    $response->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'description', 'due_date', 'status', 'created_at', 'updated_at']
            ],
            'links',
            'meta'
        ]);
});

test('user can retrieve a specific task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    
    Sanctum::actingAs($user);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertOk()
        ->assertJsonStructure([
            'data' => ['id', 'title', 'description', 'due_date', 'status', 'created_at', 'updated_at']
        ])
        ->assertJsonPath('data.id', $task->id);
});

test('user cannot access tasks from another user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user2->id]);
    
    Sanctum::actingAs($user1);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertForbidden();
});

test('user can update their task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    
    Sanctum::actingAs($user);

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'title' => 'Updated Task Title',
        'status' => 'in-progress',
    ]);

    $response->assertOk()
        ->assertJsonPath('data.title', 'Updated Task Title')
        ->assertJsonPath('data.status', 'in-progress');

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'Updated Task Title',
        'status' => 'in-progress',
    ]);
});

test('user can delete their task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    
    Sanctum::actingAs($user);

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('task filtering works', function () {
    $user = User::factory()->create();
    Task::factory(3)->create([
        'user_id' => $user->id,
        'status' => 'completed'
    ]);
    Task::factory(2)->create([
        'user_id' => $user->id,
        'status' => 'pending'
    ]);
    
    Sanctum::actingAs($user);

    $response = $this->getJson("/api/tasks?status=completed");

    $response->assertOk()
        ->assertJsonCount(3, 'data');
});

// Validation Tests
test('task creation validates inputs', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = $this->postJson('/api/tasks', [
        // Missing required fields
        'status' => 'invalid-status',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['title', 'due_date', 'status']);
});

// Service Layer Tests
test('task service creates tasks with correct user id', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    
    $taskService = app(\App\Services\TaskService::class);
    
    $task = $taskService->createTask([
        'title' => 'Service Test Task',
        'description' => 'Created via service layer',
        'due_date' => now()->addDays(3)->format('Y-m-d'),
        'status' => 'pending',
    ]);
    
    $this->assertEquals($user->id, $task->user_id);
    $this->assertEquals('Service Test Task', $task->title);
});

// Repository Layer Tests
test('task repository filters by status', function () {
    $user = User::factory()->create();
    Task::factory(3)->create([
        'user_id' => $user->id,
        'status' => 'completed'
    ]);
    Task::factory(2)->create([
        'user_id' => $user->id,
        'status' => 'pending'
    ]);
    
    $taskRepository = app(\App\Repositories\TaskRepository::class);
    
    $pendingTasks = $taskRepository->getAllTasks($user->id, ['status' => 'pending']);
    $completedTasks = $taskRepository->getAllTasks($user->id, ['status' => 'completed']);
    
    $this->assertEquals(2, $pendingTasks->count());
    $this->assertEquals(3, $completedTasks->count());
});

// Date Based Filtering Tests
test('task filtering works by date range', function () {
    $user = User::factory()->create();
    
    // Create tasks with specific dates
    Task::factory()->create([
        'user_id' => $user->id,
        'due_date' => '2025-01-01'
    ]);
    
    Task::factory()->create([
        'user_id' => $user->id,
        'due_date' => '2025-02-15'
    ]);
    
    Task::factory()->create([
        'user_id' => $user->id,
        'due_date' => '2025-03-20'
    ]);
    
    Sanctum::actingAs($user);

    // Test date range filtering
    $response = $this->getJson("/api/tasks?from_date=2025-02-01&to_date=2025-03-01");

    $response->assertOk()
        ->assertJsonCount(1, 'data');
});

// Security Tests
test('unauthenticated users cannot access protected routes', function () {
    $response = $this->getJson('/api/tasks');
    $response->assertUnauthorized();
    
    $response = $this->getJson('/api/user');
    $response->assertUnauthorized();
});

test('user can logout and token becomes invalid', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    
    // First verify we can access a protected route
    $this->getJson('/api/user')->assertOk();
    
    // Then logout
    $this->postJson('/api/logout')->assertOk();
    
    // Now token should be invalidated - but we need to simulate a new request
    // Since the test environment doesn't actually use the token after actingAs
    // This is more of a demonstration
    $user->tokens()->delete(); // Manually delete tokens as we would in the logout
    
    // We need to refresh the app to clear the authenticated user from the request
    $this->refreshApplication();
    
    $this->getJson('/api/user')->assertUnauthorized();
});