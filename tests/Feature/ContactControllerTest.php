<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\TagSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 問い合わせフォームが表示される(): void
    {
        // Arrange
        $this->seed(CategorySeeder::class);
        $this->seed(TagSeeder::class);

        $category = Category::first();
        $tag = Tag::first();

        // Act
        $response = $this->get('/');

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('contact.index');
        $response->assertViewHas('categories');
        $response->assertViewHas('tags');

        $response->assertSee($category->content);
        $response->assertSee($tag->name);
    }

    /** @test */
    public function 正しい入力で確認ページが表示される(): void
    {
        // Arrange
        $this->seed();
        $category = Category::first();
        $tag = Tag::first();

        $data = [
            'first_name' => '山田',
            'last_name' => '太朗',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '愛知県名古屋市',
            'building' => '〇〇マンション',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        // Act
        $response = $this->post('/contacts/confirm', $data);

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');
        $response->assertViewHas('validated');
        $response->assertViewHas('category');
        $response->assertViewHas('tags');
    }

    /** @test */
    public function 入力内容が不正であればエラーが発生する(): void
    {
        // Arrange
        $this->seed();
        $category = Category::first();
        $tag = Tag::first();

        $data = [
            'first_name' => '山田',
            'last_name' => '太朗',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '090123',
            'address' => '愛知県名古屋市',
            'building' => '〇〇マンション',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];
        // Act
        $response = $this->post('/contacts/confirm', $data);

        // Assert
        $response->assertSessionHasErrors('tel');
        $response->assertRedirect('/');
    }

    /** @test */
    public function 問い合わせを作成できる(): void
    {
        // Arrange

        $this->seed();

        $category = Category::first();
        $tag = Tag::first();

        $data = [
            'first_name' => '山田',
            'last_name' => '太朗',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '愛知県名古屋市',
            'building' => '〇〇マンション',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        // Act
        $response = $this->post(route('contacts.store'), $data);

        // Assert
        $response->assertRedirect(route('contact.thanks'));
        $this->assertDatabaseHas('contacts', [
            'first_name' => '山田',
            'last_name' => '太朗',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '愛知県名古屋市',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
        ]);

        $contact = Contact::where('email', 'yamada@example.com')->first();

        $this->assertDatabaseHas('contact_tag', [
            'contact_id' => $contact->id,
            'tag_id' => $tag->id,
        ]);
    }

    /** @test */
    public function 不正な情報では問い合わせを作成できない(): void
    {
        // Arrange

        $this->seed();

        $category = Category::first();
        $tag = Tag::first();

        $data = [
            'first_name' => '山田',
            'last_name' => '',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '愛知県名古屋市',
            'building' => '〇〇マンション',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        // Act
        $response = $this->post(route('contacts.store'), $data);

        // Assert
        $response->assertSessionHasErrors('last_name');
        $response->assertRedirect('/');

    }

    /** @test */
    public function サンクスページが表示される(): void
    {
        // Act
        $response = $this->get(route('contact.thanks'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('contact.thanks');

    }

    /** @test */
    public function 問い合わせを_cs_vでダウンロードできる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();

        // Act
        $response = $this->actingAs($user)->get(route('contacts.export'));

        // Assert
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition');

        $contentDisposition = $response->headers->get('Content-Disposition');

        $this->assertStringContainsString('contacts.csv', $contentDisposition);
    }

    /** @test */
    public function キーワードで検索した問い合わせを_cs_vでダウンロードできる(): void
    {
        // Arrange
        $this->seed();

        $user = User::first();
        $contact = Contact::first();

        // Act
        $response = $this->actingAs($user)->get(
            route('contacts.export', ['keyword' => $contact->first_name])
        );

        // Assert
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString($contact->first_name, $content);
    }

    /** @test */
    public function 性別で検索した問い合わせを_cs_vでダウンロードできる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();

        // Act
        $response = $this->actingAs($user)->get(
            route('contacts.export', ['gender' => $contact->gender])
        );

        // Assert
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            $contact->gender_label,
            $content
        );
    }

    /** @test */
    public function カテゴリーで検索した問い合わせを_cs_vでダウンロードできる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();

        // Act
        $response = $this->actingAs($user)->get(
            route('contacts.export', ['category_id' => $contact->category_id])
        );
        // Assert
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString(
            $contact->category->content,
            $content
        );
    }

    /** @test */
    public function 日付で検索した問い合わせを_cs_vでダウンロードできる(): void
    {
        // Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();

        // Act
        $response = $this->actingAs($user)->get(
            route('contacts.export', ['date' => $contact->created_at->toDateString(),
            ])
        );
        // Assert
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        $this->assertStringContainsString(
            $contact->created_at->toDateString(),
            $content
        );
    }
}
