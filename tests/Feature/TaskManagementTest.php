<?php

use App\Models\Task;
use App\Models\User;

test('authenticated users can view the task list page.', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/tasks');

    $response->assertStatus(200);
});

test('authenticated user can go to the create tast page.', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/tasks/create');

    $response->assertStatus(200);
});

test('authenticated users can add new task.', function () {
    $user = User::factory()->create();

    $taskData = [
        'title' => 'New Task',
        'description' => 'This is a test task.',
        'status' => 'pending'
    ];

    $response = $this->actingAs($user)->post('/tasks', $taskData);

    $response->assertRedirect('/tasks');
    $this->assertDatabaseHas('tasks', $taskData);
});

test('unauthenticated users cannot create new tasks.', function () {
    $taskData = [
        'title' => 'New Task',
        'description' => 'This is a test task.',
        'status' => 'pending'
    ];

    $response = $this->post('/tasks', $taskData);

    $response->assertRedirect('/login');
    $this->assertDatabaseMissing('tasks', $taskData);
});

test('users can update their own tasks.', function () {
    $user = User::factory()->create();

    $task = Task::factory()->create([
        'title' => 'Old Title',
        'description' => 'Old Description',
        'status' => 'pending',
    ]);

    $updatedData = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'status' => 'complete',
    ];

    $response = $this->actingAs($user)->put("/tasks/{$task->id}", $updatedData);

    $response->assertRedirect(route('tasks.index'));
    $this->assertDatabaseHas('tasks', array_merge(['id' => $task->id], $updatedData));
});

test('guest user cannot update a task and is redirected to login', function () {
    $task = Task::factory()->create();

    $updatedData = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'status' => 'completed',
    ];

    $response = $this->put("/tasks/{$task->id}", $updatedData);

    $response->assertRedirect('/login');
    $this->assertDatabaseMissing('tasks', $updatedData);
});

test('update fails if required fields are missing', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this->actingAs($user)
        ->putJson("/tasks/{$task->id}", []); // Send empty request as JSON (Simulates missing required fields.)

    $response->assertUnprocessable(); // 422 status for validation failure
});

test('users can delete their own tasks.', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

    $response->assertRedirect('/tasks');
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});


test('guest user cannot delete a task and is redirected to login', function () {
    $task = Task::factory()->create();

    $response = $this->delete("/tasks/{$task->id}");

    $response->assertRedirect('/login');
    $this->assertDatabaseHas('tasks', ['id' => $task->id]);
});
