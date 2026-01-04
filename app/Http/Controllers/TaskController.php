<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::withoutGlobalScope(\App\Models\Scopes\UserScope::class)
            ->with(['category', 'tags']);

        // $query = Task::query();

        // 認証前は全件からスタート（認証後は上記に置き換え）
        // $query = Task::query();

        // キーワード検索
        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%'.$request->keyword.'%');
        }

        // カテゴリー検索
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // タグ検索（複数）
        if ($request->filled('tag_ids')) {
            foreach ($request->tag_ids as $tagId) {
                $query->whereHas('tags', function ($q) use ($tagId) {
                    $q->where('tags.id', $tagId);
                });
            }
        }

        // ステータス検索
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 期限（開始）
        if ($request->filled('due_date_from')) {
            $query->where('due_date', '>=', $request->due_date_from);
        }

        // 期限（終了）
        if ($request->filled('due_date_to')) {
            $query->where('due_date', '<=', $request->due_date_to);
        }
        $tasks = $query->orderBy('created_at', 'desc')->paginate(10);

        $categories = Category::all();
        $tags = Tag::all();

        return view('tasks.index', compact('tasks', 'categories', 'tags'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('tasks.create', compact('categories', 'tags'));
    }

    public function store(TaskRequest $request)
    {

        $validated = $request->validated();

        // TODO: ログイン機能を実装したら auth()->id() に差し替える
        // 一旦、最初に作成したテストユーザー（ID: 1）を使用する
        $validated['user_id'] = auth()->id();

        $task = $this->taskService->createTask($validated);
        // タグを関連付ける
        if ($request->has('tags')) {
            $task->tags()->attach($request->tags);
        }

        return redirect()->route('tasks.index')->with('success', 'タスクを作成しました。');
    }

    public function show(Task $task)
    {
        // if ($task->user_id !== auth()->id()) {
        //   abort(403, 'このタスクにアクセスする権限がありません。');
        // }
        $this->authorize('view', $task);

        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        // if ($task->user_id !== auth()->id()) {
        //     abort(403, 'このタスクを編集する権限がありません。');
        // }
        $this->authorize('update', $task);
        $categories = Category::all();
        $tags = Tag::all();

        return view('tasks.edit', compact('task', 'categories', 'tags'));
    }

    public function update(TaskRequest $request, Task $task)
    {
        // if ($task->user_id !== auth()->id()) {
        //    abort(403, 'このタスクを編集する権限がありません。');
        // }
        $this->authorize('update', $task);

        $validated = $request->validated();

        $task->update($validated);

        // タグを同期する
        $task->tags()->sync($request->tags ?? []);

        return redirect()->route('tasks.show', $task)->with('success', 'タスクを更新しました。');
    }

    public function destroy(Task $task)
    {
        // if ($task->user_id !== auth()->id()) {
        //     abort(403, 'このタスクを削除する権限がありません。');
        // }
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'タスクを削除しました。');
    }
}
