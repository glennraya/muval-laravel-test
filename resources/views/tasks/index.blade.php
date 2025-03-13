<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tasks</title>
</head>
<body>
<h1>Task List</h1>
 <a href="{{ route('tasks.create') }}">
        <button>Create New Task</button>
    </a>

<ul>
    @foreach ($tasks as $task)
        <li>
            {{ $task->title }} - Assigned to: {{ $task->user->name ?? 'Unknown' }}
            {{-- NOTE: Anchor tag is inappropriate for DELETE request. Anchor tags supports only GET method. --}}
            {{-- <a href="/tasks/{{ $task->id }}/edit">Edit</a> | <a href="/tasks/{{ $task->id }}/delete">Delete</a> --}}

            <a href="/tasks/{{ $task->id }}/edit">Edit</a>

            {{-- EXPLANATION: It's inappropriate to use the <a> tag here for a DELETE request. Anchor tags supports only GET method.
                I revised this to use resourceful DELETE route, adhering to Laravel's best practice.--}}
            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display: inline;">
                @method('DELETE')
                @csrf
                <button type="submit" onclick="return confirm('Are you sure you want to delete this task?');">
                    Delete
                </button>
            </form>
        </li>
    @endforeach
</ul>
<form action="{{ route('logout') }}" method="POST" style="display: inline;">
    @csrf
    <button type="submit">Logout</button>
</form>

</body>
</html>
