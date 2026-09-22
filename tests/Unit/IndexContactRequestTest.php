<?php

namespace Tests\Unit;

use App\Http\Requests\IndexContactRequest;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい検索条件を受付ける(): void
    {
        // Arrange
        $this->seed(CategorySeeder::class);
        $data = [
            'keyword' => '佐藤',
            'gender' => 2,
            'category_id' => 1,
            'date' => '2025-03-14',
        ];

        // Act
        $validator = Validator::make(
            $data,
            (new IndexContactRequest)->rules()
        );

        // Assert
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function 不正な性別を拒否する(): void
    {
        // Arrange
        $this->seed(CategorySeeder::class);
        $data = [
            'keyword' => '佐藤',
            'gender' => 5,
            'category_id' => 1,
            'date' => '2025-03-14',
        ];

        // Act
        $validator = Validator::make(
            $data,
            (new IndexContactRequest)->rules()
        );

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('gender'));
    }
}
