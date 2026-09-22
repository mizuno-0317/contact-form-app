<?php

namespace Tests\Unit;

use App\Models\Contact;
use App\Models\Tag;
use Database\Seeders\CategorySeeder;
use Database\Seeders\TagSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function タグから複数の問い合わせが取得できる(): void
    {
        // Arrange
        $this->seed(TagSeeder::class);
        $this->seed(CategorySeeder::class);
        $tag = Tag::first();

        // Act
        $contacts = Contact::factory()->count(2)->create();
        $tag->contacts()->attach($contacts->pluck('id'));

        // Assert
        $this->assertCount(2, $tag->fresh()->contacts);
        $this->assertTrue($tag->fresh()->contacts->pluck('id')->contains($contacts->first()->id));
    }
}
