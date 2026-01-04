<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskPolicyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    public function test_所有者はタスクを更新できる()
    {
        // 準備
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $policy = new TaskPolicy;

        // 実行
        $result = $policy->update($user, $task);

        // 検証
        $this->assertTrue($result);
    }

    public function test_所有者以外はタスクを更新できない()
    {
        // 準備
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);
        $policy = new TaskPolicy;

        // 実行
        $result = $policy->update($user, $task);

        // 検証
        $this->assertFalse($result);
    }
}
