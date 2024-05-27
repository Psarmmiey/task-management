<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $statusFilter = $request->query('status');

        $query = $user->tasks();
        // apply possible filters
        if (in_array($statusFilter, ['pending', 'ongoing', 'completed'])) {
            $query->where('status', $statusFilter);
        }
        $tasks = $query->orderByRaw("
        CASE
            WHEN status = 'pending' THEN 1
            WHEN status = 'ongoing' THEN 2
            WHEN status = 'completed' THEN 3
            ELSE 4
        END ASC
    ")
            ->latest('id')
            ->cursorPaginate(10, ['*'], 'tasks');

        return inertia('Tasks/Index', [
            'tasks' => TaskResource::collection($tasks),
            'filterStatus' => $statusFilter,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTaskRequest $request)
    {
        $user = auth()->user();
        $task = $user->tasks()->create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);
        $task->fill([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'due_date' => $request->due_date,
        ]);
        $task->save();
    }

    public function updateStatus(UpdateTaskRequest $request, Task $task)
    {
        Gate::authorize('update', $task);
        $task->fill([
            'status' => $request->status,
        ]);
        $task->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        Gate::authorize('delete', $task);
        $task->delete();
    }
}
