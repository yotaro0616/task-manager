<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_ログインしたユーザーがタスク一覧ページにアクセスできる()
    {
        // 準備（Arrange）
        $user = User::factory()->create();

        // 実行（Act）
        $response = $this->actingAs($user)->get('/tasks');

        // 検証（Assert）
        $response->assertStatus(200);
    }

    public function test_ログインしたユーザーはタスク一覧ページにアクセスできる()
    {
        // 準備（Arrange）
        $user = User::factory()->create();

        // 実行（Act）
        $response = $this->actingAs($user)->get('/tasks');

        // 検証（Assert）
        $response->assertStatus(200);
    }

    public function test_task_can_be_created()
    {
        // 1. ユーザーを1人作成してログインさせる
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Factoryを使ってタスクのデータだけを生成（DBには保存しない）
        // createTaskメソッドの引数に渡すための配列を作る
        $data = [
            'title' => 'テスト用タスク',
            'description' => '詳細説明です',
            'due_date' => now()->addDay()->toDateString(),
        ];

        // 3. 実際のメソッドを呼び出す
        $taskService = new TaskService; // クラス名は適宜読み替えてください
        $result = $taskService->createTask($data);

        // 4. 検証
        $this->assertEquals('テスト用タスク', $result->title);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals('pending', $result->status);
    }

    public function test_ログインしたユーザーがタスクを作成できる()
    {
        $response = $this->actingAs($this->user)->post('/tasks', [
            'title' => 'テストタスク',
            'description' => 'テスト説明',
            'status' => 'pending',
        ]);
        $response->assertStatus(302);
        $response->assertRedirect('/tasks');

        $this->assertDatabaseHas('tasks', [
            'title' => 'テストタスク',
            'description' => 'テスト説明',
            'status' => 'pending',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_タイトルが空の場合タスクを作成できない()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', [
            'title' => '',
            'description' => 'テスト説明',
            'status' => 'pending',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('title');

        $this->assertDatabaseMissing('tasks', [
            'description' => 'テスト説明',
            'user_id' => $user->id,
        ]);
    }

    public function test_ログインしたユーザーが自分のタスク一覧を表示できる()
    {
        // 準備
        $user = User::factory()->create();
        $task1 = Task::factory()->create(['user_id' => $user->id, 'title' => 'タスク1']);
        $task2 = Task::factory()->create(['user_id' => $user->id, 'title' => 'タスク2']);
        $otherTask = Task::factory()->create(['title' => '他人のタスク']);

        // 実行
        $response = $this->actingAs($user)->get('/tasks');

        // 検証
        $response->assertStatus(200);
        $response->assertSee('タスク1');
        $response->assertSee('タスク2');
        $response->assertSee('他人のタスク');
    }

    public function test_ログインしたユーザーが自分のタスク詳細を表示できる()
    {
        // 準備
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'テストタスク',
            'description' => 'テスト説明',
        ]);

        // 実行
        $response = $this->actingAs($user)->get("/tasks/{$task->id}");

        // 検証
        $response->assertStatus(200);
        $response->assertSee('テストタスク');
        $response->assertSee('テスト説明');
    }

    public function test_ログインしたユーザーが自分のタスクを更新できる()
    {
        // 準備
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
            'title' => '元のタイトル',
        ]);

        // 実行
        $response = $this->actingAs($user)->put("/tasks/{$task->id}", [
            'title' => '更新されたタイトル',
            'description' => '更新された説明',
            'status' => 'completed',
        ]);

        // 検証
        $response->assertStatus(302);
        $response->assertRedirect(route('tasks.show', $task));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => '更新されたタイトル',
            'status' => 'completed',
        ]);
    }

    public function test_他人のタスクを更新できない()
    {
        // 準備
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
            'title' => '元のタイトル',
        ]);

        // 実行
        $response = $this->actingAs($user)->put("/tasks/{$task->id}", [
            'title' => '更新されたタイトル',
            'description' => '更新された説明',
            'status' => 'completed',
        ]);

        // 検証
        $response->assertStatus(404);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => '元のタイトル',
        ]);
    }

    public function test_ログインしたユーザーが自分のタスクを削除できる()
    {
        // 準備
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        // 実行
        $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

        // 検証
        $response->assertStatus(302);
        $response->assertRedirect('/tasks');
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_他人のタスクを削除できない()
    {
        // 準備
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        // 実行
        $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

        // 検証
        $response->assertStatus(404);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_未ログインユーザーはタスク一覧にアクセスできない()
    {
        // 実行
        $response = $this->get('/tasks');

        // 検証
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_未ログインユーザーはタスクを作成できない()
    {
        // 実行
        $response = $this->post('/tasks', [
            'title' => 'テストタスク',
            'description' => 'テスト説明',
            'status' => 'pending',
            'priority' => 3,
        ]);

        // 検証
        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('tasks', [
            'title' => 'テストタスク',
        ]);
    }

    /**
     * @dataProvider invalidTitleProvider
     */
    public function test_無効なタイトルの場合タスクを作成できない($title)
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/tasks', [
            'title' => $title,
            'description' => 'テスト説明',
            'status' => 'pending',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public static function invalidTitleProvider(): array
    {
        return [
            '空文字' => [''],
            '256文字以上' => [str_repeat('a', 256)],
        ];
    }
}
