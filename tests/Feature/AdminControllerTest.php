<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未ログインユーザーはログイン画面にリダイレクトされる(): void
    {

        // Act
        $response = $this->get(route('admin.index'));

        // Assert
        $response->assertRedirect('/login');

    }

    /** @test */
    public function 問い合わせ一覧を取得できる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();

        // Act
        $response = $this->actingAs($user)->get(route('admin.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts');

    }

    /** @test */
    public function 問い合わせは7件ずつ表示される(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();

        // Act
        $response = $this->actingAs($user)->get(route('admin.index'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts');

        $contacts = $response->ViewData('contacts');
        $this->assertCount(7, $contacts->items());

    }

    /** @test */
    public function 問い合わせをキーワードで検索できる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();

        // Act
        $response = $this->actingAs($user)->get(
            route('admin.index', ['keyword' => $contact->first_name])
        );

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts');

        $contacts = $response->viewData('contacts');

        $this->assertTrue(
            $contacts->contains('id', $contact->id)
        );
    }

    /** @test */
    public function 問い合わせを日付で検索できる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();

        // Act
        $response = $this->actingAs($user)->get(
            route('admin.index', ['date' => $contact->created_at->format('Y-m-d'),
            ])
        );

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts');

        $contacts = $response->viewData('contacts');

        $this->assertTrue(
            $contacts->contains('id', $contact->id)
        );

    }

    /** @test */
    public function 問い合わせを性別で検索できる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();

        // Act
        $response = $this->actingAs($user)->get(
            route('admin.index', ['gender' => $contact->gender])
        );

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts');

        $contacts = $response->viewData('contacts');
        $this->assertTrue(
            $contacts->contains('id', $contact->id)
        );

    }

    /** @test */
    public function 問い合わせをカテゴリで検索できる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();

        // Act
        $response = $this->actingAs($user)->get(
            route('admin.index', ['category' => $contact->category_id])
        );

        // Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts');

        $contacts = $response->viewData('contacts');
        $this->assertTrue(
            $contacts->contains('id', $contact->id)
        );
    }

    /** @test */
    public function お問い合わせ詳細を取得できる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();

        // Act
        $response = $this->actingAs($user)->get(route('admin.show', $contact));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.show');
        $response->assertViewHas('contact', $contact);
    }

    /** @test */
    public function お問い合わせを削除できる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();

        // Act
        $response = $this->actingAs($user)->delete(route('admin.destroy', $contact));

        // Assert
        $response->assertRedirect(route('admin.index'));
        $this->assertDatabaseMissing('contacts', [
            'id' => $contact->id,
        ]);
    }
}
