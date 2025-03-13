<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\TaskRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class TaskController extends Controller
{
    public function index()
    {
        // $tasks = Task::all();

        // EXPLANATION: Instead of getting all the records with ::all() method like the one above, we can select only
        // the table column that we need. This can improve the performance of the query performance dramatically.
        // The related 'user' model was also eager-loaded and only fetched the id, and name of the user.
        // This method will improve database performance overall.
        // NOTE: Of course we can also paginate the records which is much better "->paginate(10); or ->simplePaginate(10);"
        $tasks = Task::select('id', 'title', 'user_id')
            ->with('user:id,name')->get();

        foreach ($tasks as $task) {
            $task->user;
        }

        return view('tasks.index', ['tasks' => $tasks]);
    }

    public function create()
    {
        return view('tasks.create');
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        // $title = $_POST['title'];
        // $description = $_POST['description'];

        // DB::insert("INSERT INTO tasks (title, description) VALUES ('$title', '$description')");

        // return redirect()->route('tasks.index')->with('success', 'Task created successfully.');

        // EXPLANATION: Instead of using raw SQL queries above (which will work), I think it's a good idea to wrap the Eloquent
        // mass-assignment operation in a try-catch block and using DB transaction. This way, the database will rollback
        // changes made to the current database operation if there are any exceptions occurs. This is a great way
        // to prevent unwanted database changes, or orphaned model as a result of an exception.
        try {
            // Simulate server error...
            // throw new \Exception('Whoops, we got some server error...', 500);

            DB::transaction(function () use ($request) {
                // The 'user_id' field will be automatically injected whenever a task is created.
                // Please see app\Models\Task.php under the static 'boot()' method.
                Task::create($request->validated());
            });

            return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function edit(Task $task)
    {
        // EXPLANATION: Since, I've converted the routes to 'resourceful routes' instead of
        // manually declaring each route for post, get, etc. There's no need to manually
        // perform a query here just to get the task to be updated.

        // $task = DB::select("SELECT * FROM tasks WHERE id = $task->id");

        if (!$task) {
            return "Task not found";
        }

        return view('tasks.edit', ['task' => $task]);
    }

    public function update(TaskRequest $request, string $id)
    {
        // $title = $_POST['title'];
        // $description = $_POST['description'];
        // $status = $_POST['status'];

        // DB::update("UPDATE tasks SET title = '$title', description = '$description', status = '$status' WHERE id = $id");

        try {
            DB::beginTransaction();

            Task::where('id', $id)->update($request->validated());

            DB::commit();

            return redirect()->route('tasks.index');
        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }

        // return redirect()->route('tasks.index');
    }

    public function destroy(string $id)
    {
        // DB::delete("DELETE FROM tasks WHERE id = $id");

        // EXPLANATION: Instead of deleting records using raw SQL, it's much simpler
        // to do it the Laravel way like the one below. This is very easy to understand right away.
        Task::destroy($id);

        return redirect()->route('tasks.index');
    }
}
