<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Request\TagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * タグの一覧を表示
     */
    public function index()
    {
        $tags = Tag::get();
        return view('admin.index',compact('tags'));
    }

    /**
     * タグを新規作成する
     */
    public function store(TagRequest $request)
    {
        Tag::create($request->validated());
        return redirect()->route('admin.index');
    }

    /**
     * タグの編集画面を表示する
     */

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit',compact('tag'));
    }

    /**
     * タグを更新する
     */
    public function update(TagRequest $request, Tag $tag)
    {
        $tag-> update($request->validated());
        return redirect()->route('admin.index');
    }

    /**
     * タグの削除
     */
    public function destroy( Tag $tag)
    {
        $tag -> delete();
        return direct()->route('admin.index');
    }
}
