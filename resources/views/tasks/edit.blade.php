<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク編集</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        button {
            background-color: #2196f3;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .cancel-link {
            margin-left: 10px;
            color: #666;
        }
    </style>
</head>

<body>
    <h1>タスク編集</h1>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tasks.update', $task) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">タイトル</label>
            <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}" required>
        </div>

        <div class="form-group">
            <label for="description">説明</label>
            <textarea id="description" name="description" rows="4">{{ old('description', $task->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="due_date">期限</label>
            <input type="date" id="due_date" name="due_date"
                value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
        </div>

        <div class="form-group">
            <label for="status">ステータス</label>
            <select id="status" name="status">
                <option value="pending" {{ old('status', $task->status) == 'pending' ? 'selected' : '' }}>未着手</option>
                <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>進行中
                </option>
                <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>完了
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="category_id">カテゴリー</label>
            <select id="category_id" name="category_id"
                style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">選択してください</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ old('category_id', $task->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('category_id')
            <div style="color: red;">{{ $message }}</div>
        @enderror


        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">タグ</label>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach ($tags as $tag)
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                            {{ in_array($tag->id, old('tags', $task->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                        {{ $tag->name }}
                    </label>
                @endforeach
            </div>
            @error('tags')
                <div style="color: red; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">更新</button>
        <a href="{{ route('tasks.show', $task) }}" class="cancel-link">キャンセル</a>
    </form>
</body>

</html>
