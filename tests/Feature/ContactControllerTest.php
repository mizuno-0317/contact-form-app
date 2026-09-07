<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactControllerTest extends TestCase
{

    use RefreshDatabase;


    /** @test */
    public function 問い合わせを作成できる(): void
    {
        //Arrange

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

        //Act
        $response = $this->post(route('contacts.store'),$data);

        //Assert
        $response->assertRedirect(route('contact.thanks'));
        $this->assertDatabaseHas('contacts',[
            'first_name' => '山田',
            'last_name' => '太朗',
            'gender' => 1,
            'email' => 'yamada@example.com',
            'tel' => '09012345678',
            'address' => '愛知県名古屋市',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
        ]);

        $contact = Contact::where('email','yamada@example.com')->first();

        $this->assertDatabaseHas('contact_tag',[
            'contact_id'=>$contact->id,
            'tag_id'=>$tag->id,
        ]);
    }
}
