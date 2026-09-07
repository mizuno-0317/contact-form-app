<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function 問い合わせ一覧を取得できる(): void
    {
        //Arrange
        $this->seed();
        $user = User::first();

        //Act
        $response = $this->actingAs($user)->get(route('admin.index'));

        //Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts');

    }

    /** @test */
    public function 問い合わせをキーワードで検索できる(): void
    {
        //Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();

        //Act
        $response=$this->actingAs($user)->get(
            route('admin.index',['keyword'=>$contact->first_name])
        );

        //Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts');

        $contacts=$response->viewData('contacts');

        $this->assertTrue(
            $contacts->contains('id',$contact->id)
        );
    }

    /** @test */
    public function 問い合わせを日付で検索できる(): void
    {
        //Arrange
        $this->seed();
        $user=User::first();
        $contact=Contact::first();

        //Act
        $response=$this->actingAs($user)->get(
            route('admin.index',['date'=> $contact->created_at->format('Y-m-d')
            ])
        );

        //Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts');

        $contacts=$response->viewData('contacts');

        $this->assertTrue(
            $contacts->contains('id',$contact->id)
        );


    }
        /** @test */
    public function 問い合わせを性別で検索できる(): void
    {
        //Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();

        //Act
        $response = $this->actingAs($user)->get(
            route('admin.index',['gender'=>$contact->gender])
        );

        //Assert
        $response -> assertStatus(200);
        $response -> assertViewHas('contacts');

        $contacts = $response->viewData('contacts');
        $this->assertTrue(
            $contacts->contains('id',$contact->id)
        );

        }

        /** @test */
    public function 問い合わせをカテゴリで検索できる(): void
    {
        //Arrange
        $this->seed();
        $user = User::first();
        $contact = Contact::first();


        //Act
        $response = $this ->actingAs($user)->get(
            route('admin.index',['category'=> $contact->category_id])
        );

        //Assert
        $response->assertStatus(200);
        $response->assertViewHas('contacts');

        $contacts = $response->viewData('contacts');
        $this->assertTrue(
            $contacts->contains('id',$contact->id)
        );
    }

}
