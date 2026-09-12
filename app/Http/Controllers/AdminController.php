<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class AdminController extends Controller
{
    /**
     * 管理画面表示
     */
    public function index(IndexContactRequest $request)
    {

        $this->authorize('viewAny', Contact::class);

        /**
         * 検索表示
         */
        $query = Contact::query()->with(['category', 'tags']);

        /**
         * カテゴリ検索
         */
        $query->when(
            $request->filled('category_id'),
            function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            }
        );

        /**
         * 性別検索
         */
        $query->when(
            $request->filled('gender') && $request->gender != 0,
            function ($query) use ($request) {
                $query->where('gender', $request->gender);
            }
        );

        /**
         * 日付検索
         */
        $query->when(
            $request->filled('date'),
            function ($query) use ($request) {
                $query->whereDate('created_at', $request->date);
            }
        );

        /**
         * キーワード検索
         */
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // $query->when(
        //     $request->filled('keyword'),
        //     function($query)use($request){
        //         $query->where('first_name','Like',"%{$request->keyword}%")
        //         ->orWhere('last_name','Like',"%{$request->keyword}%")
        //         ->orWhere('email','Like',"%{$request->keyword}%");
        //     }
        // );

        $contacts = $query->simplePaginate(7);
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.index', compact('categories', 'tags', 'contacts'));
    }

    /**
     * お問い合わせの詳細表示
     */
    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);

        return view('admin.show', compact('contact'));
    }

    /**
     * お問い合わせの削除
     */
    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);
        $contact->delete();

        return redirect()->route('admin.index');
    }
}
