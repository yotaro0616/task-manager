<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_ユーザーを登録できる()
    {
        // 実行
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 検証
        $response->assertStatus(302);
        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);
        $this->assertAuthenticated();
    }

    public function test_重複したメールアドレスで登録できない()
    {
        // 準備
        User::factory()->create(['email' => 'test@example.com']);

        // 実行
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 検証
        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 1);
    }

    public function test_パスワードが短い場合登録できない()
    {
        // 実行
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);

        // 検証
        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_パスワード確認が一致しない場合登録できない()
    {
        // 実行
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
        ]);

        // 検証
        $response->assertStatus(302);
        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_ログインできる()
    {
        // 準備
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 実行
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // 検証
        $response->assertStatus(302);
        $response->assertRedirect('/tasks');
        $this->assertAuthenticatedAs($user);
    }

    public function test_パスワードが間違っている場合ログインできない()
    {
        // 準備
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // 実行
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // 検証
        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_存在しないユーザーでログインできない()
    {
        // 実行
        $response = $this->post('/login', [
            'email' => 'notexist@example.com',
            'password' => 'password123',
        ]);

        // 検証
        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_ログアウトできる()
    {
        // 準備
        $user = User::factory()->create();

        // 実行
        $response = $this->actingAs($user)->post('/logout');

        // 検証
        $response->assertStatus(302);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_パスワードリセットリンクを送信できる()
    {
        // 準備
        $user = User::factory()->create(['email' => 'test@example.com']);

        // 実行
        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        // 検証
        $response->assertStatus(302);
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_パスワードをリセットできる()
    {
        // 準備
        $user = User::factory()->create(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        // 実行
        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        // 検証
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        // 新しいパスワードでログインできることを確認
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    public function test_メールアドレスが無効な形式の場合登録できない()
    {
        // 実行
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 検証
        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', [
            'name' => 'テストユーザー',
        ]);
    }
}
