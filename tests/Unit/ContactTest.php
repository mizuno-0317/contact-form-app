<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Database\Seeders\CategorySeeder;
use Database\Seeders\TagSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 問い合わせからカテゴリーを取得できる(): void
    {
        // Arrange
        $this->seed(CategorySeeder::class);
        $category = Category::first();

        // Act
        $contacts = Contact::factory()->count(2)->for($category)->create();

        // Assert
        $this->assertEquals($category->id, $contacts->first()->category->id);

    }

    /** @test */
    public function 問い合わせに複数のタグを紐づけられる(): void
    {
        // Arrange
        $this->seed(CategorySeeder::class);
        $this->seed(TagSeeder::class);
        $tag1 = Tag::first();
        $tag2 = Tag::skip(1)->first();

        // Act
        $contact = Contact::factory()->create();
        $contact->tags()->attach([$tag1->id, $tag2->id]);

        // Assert
        $this->assertCount(2, $contact->fresh()->tags);
        $this->assertTrue($contact->tags->pluck('id')->contains($tag1->id));
    }
}
