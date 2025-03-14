<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\TaskRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TaskApiController extends Controller
{
    public function index()
    {
        $tasks = Task::select('id', 'title', 'description', 'status', 'user_id')
            ->where('user_id', auth()->id())
            ->with('user:id,name')
            ->get();

        return response()->json([
            'tasks' => $tasks
        ]);
    }

    public function edit(Task $task)
    {
        if (!$task) {
            return "Task not found";
        }

        return response()->json([
            'task' => $task
        ]);
    }

    public function store(TaskRequest $request): JsonResponse
    {
        try {
            // Simulate server error...
            // throw new \Exception('Whoops, we got some server error...', 500);

            DB::transaction(function () use ($request) {
                // The authenticated user's id will be automatically injected whenever a task is created.
                // Please see app\Models\Task.php under the static 'boot()' method.
                Task::create($request->validated());
            });

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function update(TaskRequest $request, string $id)
    {
        try {
            DB::beginTransaction();

            Task::where('id', $id)->update($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();

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

        return response()->json([
            'success' => true,
        ]);
    }
}
