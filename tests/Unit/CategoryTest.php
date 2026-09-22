<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function カテゴリーから複数のお問い合わせを取得できる(): void
    {
        // Arrange
        $this->seed(CategorySeeder::class);
        $category = Category::first();

        // Act
        Contact::factory()->count(2)->for($category)->create();

        // Assert
        $this->assertCount(2, $category->fresh()->contacts);
        $this->assertInstanceOf(Contact::class, $category->fresh()->contacts->first());
    }
}
