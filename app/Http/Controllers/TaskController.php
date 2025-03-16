<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function index()
    {
        // $tasks = Task::all();

        // EXPLANATION: Instead of getting all the records with ::all() method like the one above, we select only
        // the table column that we need. This can improve the performance of the query significantly.
        // The related 'user' model was also eager-loaded and only fetched the id, and name of the user.
        // This technique will improve database performance overall.
        // Also, it will only return all the tasks belonging to the authenticated user.
        // NOTE: Of course we can also paginate the records which is much better "->paginate(10); or ->simplePaginate(10);"
        $tasks = Task::select('id', 'title', 'user_id')
            ->where('user_id', auth()->id())
            ->with('user:id,name')
            ->orderBy('id', 'desc')
            ->get();

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
        // to prevent unwanted database changes, or orphaned models as a result of an exception.
        try {
            // Simulate server error...
            // throw new \Exception('Whoops, we got some server error...', 500);

            DB::transaction(function () use ($request) {
                // The authenticated user's id will be automatically injected whenever a task is created.
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
        // EXPLANATION: Since, I've converted the routes to 'resourceful routing' instead of
        // manually declaring each route for post, get, etc. There's no need to manually
        // perform a query here just to get the task to be updated.

        // $task = DB::select("SELECT * FROM tasks WHERE id = $task->id");

        if (! $task) {
            return 'Task not found';
        }

        return view('tasks.edit', ['task' => $task]);
    }

    public function update(TaskRequest $request, string $id)
    {
        // $title = $_POST['title'];
        // $description = $_POST['description'];
        // $status = $_POST['status'];

        // DB::update("UPDATE tasks SET title = '$title', description = '$description', status = '$status' WHERE id = $id");

        // IMPROVEMENTS:
        // - Reused the TaskRequest class for input validation.
        // - Used DB transaction to rollback database changes as a result of an exception.
        // - Instead of using raw SQL query to update data, I used eloquent query.
        // - Wrap the entire update function inside a try-catch block to catch exceptions.
        try {
            DB::transaction(function () use ($request, $id) {
                Task::where('id', $id)->update($request->validated());
            });

            return redirect()->route('tasks.index');
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function destroy(string $id)
    {
        // DB::delete("DELETE FROM tasks WHERE id = $id");

        // IMPROVEMENT:
        // - Deleting a recording using Eloquent is much simpler and easy to
        //   understand instead of using raw SQL queries like the one above.
        Task::destroy($id);

        return redirect()->route('tasks.index');
    }
}
