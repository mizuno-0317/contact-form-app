<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;

class ContactController extends Controller
{

    /**
     * 問い合わせの作成画面を表示
     */

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view ('contact.index',compact('categories','tags'));
    }

    /**
     * 確認画面の表示
     */

    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();
        $category = Category::find($validated['category_id']);
        $tags = Tag::whereIn('id',$validated['tag_ids'])->get();

        return view ('contact.confirm',compact('validated','category','tags'));
    }

    /**
     * 問い合わせ内容を作成
    */

    public function store(StoreContactRequest $request)
    {

        $validated = $request->validated();

        $tagIds = $validated['tag_ids'] ?? [];
        unset($validated['tag_ids']);

        $contact = Contact::create($validated);

        $contact->tags()->sync($tagIds);

        return redirect()->route('contact.thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }



}
