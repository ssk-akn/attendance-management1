<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_email_is_required()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login',[
            // 'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email']);
        $errors = session('errors');
        $this->assertEquals('メールアドレスを入力してください', $errors->first('email'));
    }

    public function test_password_is_required()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login',[
            'email' => 'test@example.com',
            // 'password' => 'password123',
        ]);
        $response->assertSessionHasErrors(['password']);
        $errors = session('errors');
        $this->assertEquals('パスワードを入力してください', $errors->first('password'));
    }

    public function test_invalid_credentials_show_error_message()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email']);
        $errors = session('errors');
        $this->assertEquals('ログイン情報が登録されていません', $errors->first('email'));
    }
}
