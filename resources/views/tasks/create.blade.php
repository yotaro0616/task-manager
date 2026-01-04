<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク作成</title>
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
            background-color: #4CAF50;
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
    <h1>タスク作成</h1>

    @if ($errors->any())
        <div class="error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-alert type="success" />

    <form method="POST" action="{{ route('tasks.store') }}">
        @csrf

        <div class="form-group">
            <label for="title">タイトル</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
        </div>

        <div class="form-group">
            <label for="description">説明</label>
            <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <label for="due_date">期限</label>
            <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}">
        </div>

        <div class="form-group">
            <label for="status">ステータス</label>
            <select id="status" name="status">
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>未着手</option>
                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>進行中</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>完了</option>
            </select>
        </div>

        <div class="form-group">
            <label for="category_id">カテゴリー</label>
            <select id="category_id" name="category_id"
                style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">選択してください</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label style="display: block; margin-bottom: 5px; font-weight: bold;">タグ</label>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                @foreach ($tags as $tag)
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                            {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
                        {{ $tag->name }}
                    </label>
                @endforeach
            </div>
            @error('tags')
                <div style="color: red; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit">作成</button>
        <a href="{{ route('tasks.index') }}" class="cancel-link">キャンセル</a>
    </form>
</body>

</html>
