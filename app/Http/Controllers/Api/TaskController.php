<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskCollection;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('user_id', Auth::id())->get();

        return new TaskCollection($tasks);
    }

    public function store(TaskRequest $request): JsonResponse
    {
        $task = Task::create([
            'user_id' => Auth::id(),
            ...$request->validated(),
        ]);

        return response()->json(['data' => $task], 201);
    }

    public function show(string $id): JsonResponse
    {
        $task = Task::findOrFail($id);

        if (! $task) {
            return response()->json([
                'message' => 'Task not found',
                'error' => 'TASK_NOT_FOUND',
                'status' => 404,
            ], 404);
        }

        return response()->json(['data' => $task], 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $task = Task::where('user_id', Auth::id())->find($id);

        if (! $task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'status' => 'sometimes|required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $task->update($validated);

        return response()->json([
            'message' => 'タスクを更新しました',
            'data' => $task,
        ], 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);
        if (! $task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $deletedTask = $task->toArray();
        $task->delete();

        return response()->json([
            'message' => '削除しました',
            'data' => $deletedTask,
        ], 200);
    }
}
