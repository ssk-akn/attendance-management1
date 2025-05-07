<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_name_is_required()
    {
        $response = $this->post('/register',[
            // 'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
        $errors = session('errors');
        $this->assertEquals('お名前を入力してください', $errors->first('name'));
    }

    public function test_email_is_required()
    {
        $response = $this->post('/register',[
            'name' => 'Test User',
            // 'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $errors = session('errors');
        $this->assertEquals('メールアドレスを入力してください', $errors->first('email'));
    }

    public function test_password_must_be_at_least_8_characters()
    {
        $response = $this->post('/register',[
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $errors = session('errors');
        $this->assertEquals('パスワードは8文字以上で入力してください', $errors->first('password'));
    }

    public function test_password_must_match_confirmation()
    {
        $response = $this->post('/register',[
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password987',
        ]);
        $response->assertSessionHasErrors(['password']);
        $errors = session('errors');
        $this->assertEquals('パスワードと一致しません', $errors->first('password'));
    }

    public function test_password_is_required()
    {
        $response = $this->post('/register',[
            'name' => 'Test User',
            'email' => 'test@example.com',
            // 'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['password']);
        $errors = session('errors');
        $this->assertEquals('パスワードを入力してください', $errors->first('password'));
    }

    public function test_user_can_register_with_valid_date()
    {
        $response = $this->post('/register',[
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
