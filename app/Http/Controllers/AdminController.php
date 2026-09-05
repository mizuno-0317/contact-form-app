<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Requests\IndexContactRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * 管理画面表示
     */
    public function index(IndexContactRequest $request)
    {
        $categories = Category::all();
        $tags = Tag::all();


        /**
         * 検索表示
         */

        $query = Contact::query();

        /**
         * カテゴリ検索
         */

        $query->when(
            $request->filled('category'),
            function($query)use($request){
                $query->where('category_id',$request->category);
            }
        );

        /**
         * 性別検索
         */

        $query->when(
            $request->filled('gender') && $request->gender !=0,
            function($query)use($request){
                $query->where('gender',$request->gender);
            }
        );

        /**
         * 日付検索
         */

        $query->when(
            $request->filled('date'),
            function($query)use($request){
                $query->whereDate('created_at',$request->date);
            }
        );

        /**
         * キーワード検索
         */

        $query->when(
            $request->filled('keyword'),
            function($query)use($request){
                $query->where('first_name','Like',"%{$request->keyword}%")
                ->orWhere('last_name','Like',"%{$request->keyword}%")
                ->orWhere('email','Like',"%{$request->keyword}%");
            }
        );

        $contacts = $query->simplePaginate(7);

        return view('admin.index',compact('categories','tags','contacts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * お問い合わせの詳細表示
     */
    public function show(Contact $contact)
    {
        return view ('admin.show',compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * お問い合わせの削除
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return redirect()->route('admin.index');
    }
}
