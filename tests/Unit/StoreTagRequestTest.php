<?php

namespace Tests\Unit;

use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreTagRequestTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正しい入力ならばバリデーションが通過する(): void
    {
        // Arrange
        $data = [
            'name' => '新規作成',
        ];
        // Act
        $validator = Validator::make(
            $data,
            (new StoreTagRequest)->rules()
        );
        // Assert
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function 新規登録時にすでに他で使用されているタグは拒否される(): void
    {
        // Arrange
        $tag = Tag::create([
            'name' => '重複',
        ]);

        $data = [
            'name' => '重複',
        ];

        // Act
        $validator = Validator::make(
            $data,
            (new StoreTagRequest)->rules()
        );

        // Assert
        $this->assertTrue($validator->fails());
    }

    /** @test */
    public function 自分自身は重複して扱わない(): void
    {
        // Arrange
        $tag = Tag::create(['name' => '確認中']);
        $data = ['name' => '確認中'];

        $request = new StoreTagRequest;
        $request->merge(['tag' => $tag]);

        // Act
        $validator = Validator::make(
            $data,
            $request->rules()
        );

        // Assert
        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function すでに他で使用されているタグは更新時に拒否される(): void
    {
        // Arrange
        $tag1 = Tag::create(['name' => '変更前']);
        $tag2 = Tag::create(['name' => 'すでに使用中']);
        $data = ['name' => 'すでに使用中'];

        $request = new StoreTagRequest;
        $request->merge(['tag' => $tag1]);

        // Act
        $validator = Validator::make(
            $data,
            $request->rules()
        );

        // Assert
        $this->assertTrue($validator->fails());
    }

    public function タグの名前が51文字以上は拒否される(): void
    {
        // Arrange
        $data = [
            'name' => str_repeat('あ', 51),
        ];
        $request = new StoreTagRequest;

        // Act
        $validator = Validator::make(
            $data,
            $request->rules()
        );

        // Assert
        $this->assertTrue($validator->fails());
        $this->assertTrue($validator->errors()->has('name'));
    }
}
