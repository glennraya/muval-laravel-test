# Muval Laravel Test Report

>Note: Please check my refactored code. I got a lot of comments there to explain it further.

## Table of Contents

- [Muval Laravel Test Report](#muval-laravel-test-report)
  - [Table of Contents](#table-of-contents)
  - [Issues Found](#issues-found)
    - [Routing](#routing)
    - [Showing List of Tasks](#showing-list-of-tasks)
      - [Issues](#issues)
    - [Creating Task](#creating-task)
      - [Issues:](#issues-1)
      - [Fix/Improvement:](#fiximprovement)
    - [Updating Task](#updating-task)
      - [Issues](#issues-2)
    - [Deleting Task](#deleting-task)
      - [Issue](#issue)
    - [Validation](#validation)
  - [API Route and Controller](#api-route-and-controller)
  - [Tests](#tests)

## Issues Found

### Routing
Instead of declaring each request like the code below, I simplified this using resourceful routing/controller:

**Before**
```
Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
Route::post('/tasks/store', [TaskController::class, 'store'])->name('tasks.store');
Route::get('/tasks/{id}/edit', [TaskController::class, 'edit']);
Route::post('/tasks/update/{id}', [TaskController::class, 'update']);
Route::get('/tasks/{id}/delete', [TaskController::class, 'destroy']);
```

**After**
```
Route::resource('tasks', TaskController::class);
```
Using resourceful routing, we can declare all of the same routes from before, all with a single line of code.


### Showing List of Tasks
#### Issues
* `index()` is producing the N+1 query problem:
```
$tasks = Task::all();
foreach ($tasks as $task) {
	$task->user;
}
```
Here, the original code is performing a query to get all the tasks, and looping through all those tasks one-by-one to get the associated user. This is a bad practice, and will repeat the same query as many times as the number of tasks to be fetched unnecessarily. And it's also fetching tasks that are not owned by the authenticated user.

**Fix**
```
 $tasks = Task::select('id', 'title', 'user_id')
     ->where('user_id', auth()->id())
     ->with('user:id,name')
     ->orderBy('id', 'desc')
     ->get();
```
I fixed the issues using the following query. This will only fetch the fields needed to display the tasks that are owned by the authenticated user, eager-loaded the user model who owns the tasks, and ordered them with the latest entry at the top.


### Creating Task
#### Issues:
* `status` field and `user_id` doesn't have a default value
```
DB::insert("INSERT INTO tasks (title, description) VALUES ('$title', '$description')");
```
* `store()` doesn't have proper validation in place.

#### Fix/Improvement:
* Created a `app\Http\Requests\TaskRequest.php` file where validation will take place. Including a default value for the `status` field. Using this approach, the request validation can be re-used in `store()` and `update()` methods.
* It will automatically inject the authenticated user's id to fill up the `user_id` field.
* Provided validation error message at the top of the create task form if required field(s) are not provided.
* Provided proper return type (optional).
* Instead of using raw SQL query for inserting data, I used Eloquent's **mass-assignment** operation, and used **database transaction** to preserve database integrity when exception occurs. **Please see my comments in the code.**

New `store()` function:
```
    try {
        DB::transaction(function () use ($request) {
            // The authenticated user's id will be automatically injected whenever a task is created.
            // Please see app\Models\Task.php under the static 'boot()' method.
            Task::create($request->validated());
        });

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    } catch (\Exception $exception) {
        throw $exception;
    }
```

### Updating Task
#### Issues
* `update()` method doesn't have proper validation in place.
* It issues raw SQL query:
```
DB::update("UPDATE tasks SET title = '$title', description = '$description', status = '$status' WHERE id = $id");
```
Although this would work just fine, but updating models with lots of fields would be cumbersome. Using eloquent techniques will solve this problem.
```
try {
    DB::transaction(function () use ($request) {
        Task::create($request->validated());
    });

    return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
} catch (\Exception $exception) {
    throw $exception;
}
```
First, I wrapped the query inside a try-catch block to catch an exception if it occurs, then used the database transaction method to rollback changes made to the database in case of an error to preserve database integrity. I also used form request validation here.

### Deleting Task
#### Issue
* Using raw SQL query:
`DB::delete("DELETE FROM tasks WHERE id = $id");`

Simplified this using eloquent query:
`Task::destroy($id);`

### Validation
The backend doesn't have any validation in place whenever we are creating/updating records. I created a `TaskRequest.php` form request file where we can declare our validation logic and reuse this same file in `store()` and `update()` methods.

**File Location**
`app\Http\Requests\TasksRequest.php`

## API Route and Controller

Since this Laravel backend is also required to provide API endpoints for a Vue 3 SPA Task Management Application, I've created a separate `TaskApiController.php`, `RegistrationApiController.php`, `LoginApiController.php` and a new route file `api.php` to provide the endpoints for our frontend to consume.

I also installed and configured **Laravel Sanctum** to provide us with the authentication system for our frontend. Below is the location of the new controller and the api route file.

```
// Controllers
app/Http/Controllers/TaskApiController.php
app/Http/Controllers/RegistrationApiController.php
app/Http/Controllers/LoginApiController.php

// API route file
routes/api.php
```

## Tests
I've included a number of test cases for authentication, registration and for the task management itself using PestPHP. You can run the test using the following command:

```
php artisan test
```

**Test files directory**

```
tests/Feature
```
