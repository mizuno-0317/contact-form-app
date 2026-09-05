<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreTagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * タグの一覧を表示
     */
    public function index()
    {
        $tags = Tag::get();
        $this->authorize('viewAny',Tag::class);
        return view('admin.index',compact('tags'));
    }

    /**
     * タグを新規作成する
     */
    public function store(StoreTagRequest $request)
    {
        $this->authorize('create',Tag::class);
        Tag::create($request->validated());
        return redirect()->route('admin.index');
    }

    /**
     * タグの編集画面を表示する
     */

    public function edit(Tag $tag)
    {
        $this->authorize('view',$tag);
        return view('admin.tags.edit',compact('tag'));
    }

    /**
     * タグを更新する
     */
    public function update(StoreTagRequest $request, Tag $tag)
    {
        $this->authorize('update',$tag);
        $tag-> update($request->validated());
        return redirect()->route('admin.index');
    }

    /**
     * タグの削除
     */
    public function destroy( Tag $tag)
    {
        $this->authorize('delete',$tag);
        $tag -> delete();
        return redirect()->route('admin.index');
    }
}
