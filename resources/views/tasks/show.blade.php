<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク詳細</title>
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
            width: 150px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #2196f3;
            text-decoration: none;
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
    </style>
</head>

<body>
    <h1>タスク詳細</h1>

    @if (session('success'))
        <div style="color: green; margin-bottom: 15px;">
            {{ session('success') }}
        </div>
    @endif

    <table border="1" style="border-collapse: collapse; width: 100%;">
        <tr>
            <th style="padding: 10px; background-color: #f5f5f5;">タイトル</th>
            <td style="padding: 10px;">{{ $task->title }}</td>
        </tr>
        <tr>
            <th style="padding: 10px; background-color: #f5f5f5;">説明</th>
            <td style="padding: 10px;">{{ $task->description ?? '未設定' }}</td>
        </tr>

        <tr>
            <th style="padding: 10px; background-color: #f5f5f5;">カテゴリー</th>
            <td style="padding: 10px;">
                {{ $task->category?->name ?? '未分類' }}
            </td>
        </tr>

        <tr>
            <th style="padding: 10px; background-color: #f5f5f5;">タグ</th>
            <td style="padding: 10px;">
                @forelse ($task->tags as $tag)
                    <span
                        style="display: inline-block; background-color: #e0e0e0; padding: 4px 8px; margin: 2px; border-radius: 4px; font-size: 0.9em;">{{ $tag->name }}</span>
                @empty
                    <span style="color: #999;">タグなし</span>
                @endforelse
            </td>
        </tr>

        <tr>
            <th style="padding: 10px; background-color: #f5f5f5;">ステータス</th>
            <td style="padding: 10px;">{{ $task->status }}</td>
        </tr>

        <tr>
            <th style="padding: 10px; background-color: #f5f5f5;">期限</th>
            <td style="padding: 10px;">{{ $task->due_date?->format('Y-m-d') ?? '未設定' }}</td>
        </tr>
    </table>

    <div style="margin-top: 20px;">
        @can('update', $task)
            <a href="{{ route('tasks.edit', $task) }}"
                style="display: inline-block; padding: 10px 20px; background-color: #2196f3; color: white; text-decoration: none; border-radius: 4px;">編集</a>
        @endcan

        @can('delete', $task)
            <form method="POST" action="{{ route('tasks.destroy', $task) }}" style="display: inline;"
                onsubmit="return confirm('本当に削除しますか？');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    style="padding: 10px 20px; background-color: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer;">削除</button>
            </form>
        @endcan

        <a href="{{ route('tasks.index') }}" style="margin-left: 10px; color: #666;">← 一覧に戻る</a>
    </div>
</body>

</html>
