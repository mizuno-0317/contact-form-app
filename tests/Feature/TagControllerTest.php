<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Database\Seeders\TagSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 認証済みユーザーがタグの編集画面を表示できる(): void
    {
        // Arrange
        $this->seed(TagSeeder::class);
        $this->seed(UserSeeder::class);
        $user = User::first();
        $tag = Tag::first();
        // Act
        $response = $this->actingAs($user)->get(route('tags.edit', $tag));
        // Assert
        $response->assertStatus(200);
    }

    /** @test */
    public function 認証済みユーザーはタグの作成ができる(): void
    {
        // Arrange
        $this->seed(UserSeeder::class);
        $user = User::first();

        $data = [
            'name' => '新しいタグ',
        ];

        // Act
        $response = $this->actingAs($user)->post(route('tags.store', $data));

        // Assert
        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseHas('tags', ['name' => '新しいタグ']);
    }

    /** @test */
    public function 認証済みユーザーがタグの更新ができる(): void
    {
        // Arrange
        $this->seed(UserSeeder::class);
        $this->seed(TagSeeder::class);

        $user = User::first();
        $tag = Tag::first();
        $data = [
            'name' => '更新後のタグ',
        ];

        // Act
        $response = $this->actingAs($user)->put(route('tags.update', $tag), $data);

        // Assert
        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseHas('tags', ['name' => '更新後のタグ']);
    }

    /** @test */
    public function 認証済みユーザーはタグを削除できる(): void
    {
        // Arrange
        $this->seed(UserSeeder::class);
        $this->seed(TagSeeder::class);

        $user = User::first();
        $tag = Tag::first();

        // Act

        $response = $this->actingAs($user)->delete(route('tags.destroy', $tag));
        // Assert
        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    /** @test */
    public function 未認証のユーザーはタグ作成の操作ができない(): void
    {
        // Arrange
        $data = ['name' => '新しいタグ'];

        // Act
        $response = $this->post(route('tags.store'), $data);
        // Assert
        $response->assertRedirect('/login');
    }

    /** @test */
    public function 未認証のユーザーはタグ更新の操作ができない(): void
    {
        // Arrange
        $this->seed(TagSeeder::class);
        $tag = Tag::first();
        $data = ['name' => '更新後のタグ'];

        // Act
        $response = $this->put(route('tags.update', $tag), $data);
        // Assert
        $response->assertRedirect('/login');
    }

    /** @test */
    public function 未認証のユーザーはタグ削除の操作ができない(): void
    {
        // Arrange
        $this->seed(TagSeeder::class);
        $tag = Tag::first();

        // Act
        $response = $this->delete(route('tags.destroy', $tag));
        // Assert
        $response->assertRedirect('/login');
    }
}
