<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskControllerApi extends Controller
{
    public function index()
    {
        // $tasks = Task::all();

        // EXPLANATION: Instead of getting all the records with ::all() method like the one above, we select only
        // the table column that we need. This can improve the performance of the query dramatically.
        // The related 'user' model was also eager-loaded and only fetched the id, and name of the user.
        // This technique will improve database performance overall.
        // It will only return all the tasks belonging to the authenticated user.
        // NOTE: Of course we can also paginate the records which is much better "->paginate(10); or ->simplePaginate(10);"
        $tasks = Task::select('id', 'title', 'description', 'user_id')
            ->where('user_id', auth()->id())
            ->with('user:id,name')
            ->get();

        return response()->json([
            'tasks' => $tasks
        ]);
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

        return response()->json([
            'task' => $task
        ]);
    }
}
