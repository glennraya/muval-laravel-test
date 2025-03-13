<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Task</title>
</head>
<body>
    <h1>Create a New Task</h1>

    {{-- IMPROVEMENT: Provided validation error messages if there's any --}}
    @if ($errors->any())
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- EXPLANATION: I converted the routes to resourceful route (Laravel's best practice), I changed the action route from '/tasks/store' to simply '/tasks'. Resourceful controller/routing is the recommended approach for this. --}}
    <form action="{{route('tasks.create') }}" method="POST">
        @csrf
        <label for="title">Title:</label>
        <input type="text" id="title" name="title"><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description"></textarea><br>

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select><br>

        <button type="submit">Create Task</button>
    </form>

    <a href="/tasks">Back to Task List</a>
</body>
</html>
