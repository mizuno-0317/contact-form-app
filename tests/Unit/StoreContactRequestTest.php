<?php

namespace Tests\Unit;

use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Tag;
use Database\Seeders\CategorySeeder;
use Database\Seeders\TagSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreContactRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい入力ならばバリデーションが通過する(): void
    {
        // Arrange
        $this->seed(CategorySeeder::class);
        $this->seed(TagSeeder::class);

        $data = [
            'first_name' => '佐藤',
            'last_name' => 'さとう',
            'gender' => 2,
            'email' => 'sato@example.com',
            'tel' => '09012349876',
            'address' => '山形県山形市',
            'building' => 'やまがたビル',
            'category_id' => Category::first()->id,
            'detail' => 'お問い合わせです',
            'tag_ids' => [Tag::first()->id],
        ];

        // Act
        $validator = Validator::make(
            $data,
            (new StoreContactRequest)->rules()
        );

        // Assert
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function 電話番号が不正ならエラーになる(): void
    {
        // Arrange
        $this->seed(TagSeeder::class);
        $this->seed(CategorySeeder::class);
        $data = [
            'first_name' => '佐藤',
            'last_name' => 'さとう',
            'gender' => 2,
            'email' => 'sato@example.com',
            'tel' => '0901234',
            'address' => '山形県山形市',
            'building' => 'やまがたビル',
            'category_id' => Category::first()->id,
            'detail' => 'お問い合わせです',
            'tag_ids' => [Tag::first()->id],
        ];

        // Act
        $validator = Validator::make(
            $data,
            (new StoreContactRequest)->rules()
        );

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('tel'));
    }
}
