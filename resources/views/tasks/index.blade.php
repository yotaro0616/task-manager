<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク一覧</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .status-pending {
            color: #ff9800;
        }

        .status-in_progress {
            color: #2196f3;
        }

        .status-completed {
            color: #4caf50;
        }

        /* 操作ボタンの共通スタイル */
        .btn-action {
            display: inline-block;
            padding: 4px 8px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            margin-right: 4px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-action:hover {
            opacity: 0.8;
        }

        /* 詳細ボタン（グレー） */
        .btn-show {
            background-color: #607d8b;
            color: white;
        }

        /* 編集ボタン（青） */
        .btn-edit {
            background-color: #2196f3;
            color: white;
        }

        /* 削除ボタン（赤） */
        .btn-delete {
            background-color: #f44336;
            color: white;
            border: none;
        }

        /* 操作列のセルをボタンが並ぶように調整 */
        .actions-cell {
            white-space: nowrap;
            /* ボタンが改行されないようにする */
        }
    </style>
</head>

<body>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1>タスク一覧</h1>
        <div>
            <span>{{ auth()->user()->name }}さん</span>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                @csrf
                <button type="submit"
                    style="background: none; border: none; color: #f44336; cursor: pointer;">ログアウト</button>
            </form>
        </div>
    </div>

    <form method="GET" action="{{ route('tasks.index') }}"
        style="margin-bottom: 20px; padding: 15px; background-color: #f5f5f5; border-radius: 4px;">
        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <input type="text" name="keyword" placeholder="タイトルで検索" value="{{ request('keyword') }}"
                style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            <select name="category_id" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">すべてのカテゴリー</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            <div>
                <label>タグ</label>
                @foreach ($tags as $tag)
                    <label>
                        <input type="checkbox" name="tag_ids[]" value="{{ $tag->id }}"
                            {{ in_array($tag->id, request('tag_ids', [])) ? 'checked' : '' }}>
                        {{ $tag->name }}
                    </label>
                @endforeach
            </div>
            <select name="status" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">すべてのステータス</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>未着手</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>進行中</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>完了</option>
            </select>
            <label style="display: flex; align-items: center; gap: 5px;">
                期限:
                <input type="date" name="due_date_from" value="{{ request('due_date_from') }}"
                    style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                〜
                <input type="date" name="due_date_to" value="{{ request('due_date_to') }}"
                    style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </label>
            <button type="submit"
                style="padding: 8px 16px; background-color: #2196f3; color: white; border: none; border-radius: 4px; cursor: pointer;">検索</button>
            <a href="{{ route('tasks.index') }}" style="padding: 8px 16px; color: #666; text-decoration: none;">クリア</a>
        </div>
    </form>

    <x-alert type="success" />

    <a href="{{ route('tasks.create') }}"
        style="display: inline-block; margin-bottom: 15px; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px;">新規作成</a>

    <p style="margin-bottom: 10px;">検索結果: {{ $tasks->total() }}件</p>

    @if ($tasks->isEmpty())
        <p>タスクがありません。</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>タイトル</th>
                    <th>カテゴリー</th>
                    <th>タグ</th>{{-- 追加 --}}
                    <th>ステータス</th>
                    <th>期限</th>
                    <th>作成日時</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    <tr>
                        <td>{{ $task->id }}</td>
                        <td>{{ $task->title }}</td>
                        <td style="padding: 10px;">{{ $task->category?->name ?? '未分類' }}</td> {{-- 追加 --}}
                        <td style="padding: 10px;">
                            @foreach ($task->tags as $tag)
                                <span
                                    style="display: inline-block; background-color: #e0e0e0; padding: 2px 6px; margin: 1px; border-radius: 3px; font-size: 0.85em;">{{ $tag->name }}</span>
                            @endforeach
                        </td>
                        <td class="status-{{ $task->status }}">
                            @if ($task->status === 'pending')
                                未着手
                            @elseif($task->status === 'in_progress')
                                進行中
                            @elseif($task->status === 'completed')
                                完了
                            @endif
                        </td>
                        <td>{{ $task->due_date ? $task->due_date->format('Y年m月d日') : '未設定' }}</td>
                        <td>{{ $task->created_at->format('Y年m月d日 H:i') }}</td>
                        <td class="actions-cell">
                            {{-- 詳細権限（view）がある場合のみ表示 --}}
                            @can('view', $task)
                                <a href="{{ route('tasks.show', $task) }}" class="btn-action btn-show">詳細</a>
                            @endcan

                            {{-- 編集権限（update）がある場合のみ表示 --}}
                            @can('update', $task)
                                <a href="{{ route('tasks.edit', $task) }}" class="btn-action btn-edit">編集</a>
                            @endcan

                            {{-- 削除権限（delete）がある場合のみ表示 --}}
                            @can('delete', $task)
                                <form method="POST" action="{{ route('tasks.destroy', $task) }}" style="display: inline;"
                                    onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">削除</button>
                                </form>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $tasks->appends(request()->query())->links('pagination::simple-tailwind') }}
    @endif
</body>

</html>
