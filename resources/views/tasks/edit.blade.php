<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
</head>
<body>
    <h1>Edit Task</h1>

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

    {{-- EXPLANATION: To conform to Laravel's best practice, like the other routes, I converted this to 'resourceful route'. I put the @method('PATCH') here. In traditional HTML, the method attribute in the form tag only supports either POST or GET. Laravel
    provides us with 'method spoofing'. So the @method('PATCH') tells Laravel that this is a PATCH method. --}}

    {{-- Original: <form action="/tasks/update/{{ $task->id }}" method="POST"> --}}
    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
        {{-- This is the method spoofing provided by Laravel. --}}
        @method('PATCH')

        @csrf
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="{{ $task->title }}"><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description">{{ $task->description }}</textarea><br>

        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="pending" {{ $task->status == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed" {{ $task->status == 'completed' ? 'selected' : '' }}>Completed</option>
        </select><br>

        <!-- Using inline JavaScript (not recommended) -->
        <button type="submit" onclick="return confirm('Are you sure you want to save changes?')">Save</button>
    </form>

    <a href="/tasks">Back to Task List</a>
</body>
</html>
