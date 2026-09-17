<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Requests\Api\V1\StoreContactRequest;
use App\Http\Requests\Api\V1\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexContactRequest $request)
    {

        $query = Contact::query()->with(['category', 'tags']);

        /**
         * カテゴリ検索
         */
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        /**
         * 性別検索
         */
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        /**
         * 日付検索
         */
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

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

        $perPage = $request->input('per_page', 20);
        $contacts = $query->paginate($perPage);
        // $categories = Category::all();
        // $tags = Tag::all();

        return ContactResource::collection($contacts);
    }

    /**
     * お問い合わせ新規作成
     */
    public function store(StoreContactRequest $request)
    {
        $validated = $request->validated();

        $tagIds = $validated['tag_ids'] ?? [];
        unset($validated['tag_ids']);
        $contact = Contact::create($validated);

        $contact->tags()->sync($tagIds);

        $contact->load(['category', 'tags']);

        return (new ContactResource($contact))
            ->response()
            ->setStatusCode(201);

    }

    /**
     * お問い合わせ詳細表示
     */
    public function show(Contact $contact)
    {
        $contact->load(['category', 'tags']);

        return new ContactResource($contact);
    }

    /**
     * お問い合わせの更新
     */
    public function update(UpdateContactRequest $request, Contact $contact)
    {
        $validated = $request->validated();

        $tagIds = $validated['tag_ids'] ?? [];
        unset($validated['tag_ids']);
        $contact->update($validated);

        $contact->tags()->sync($tagIds);

        $contact->load(['category', 'tags']);

        return new ContactResource($contact);

    }

    /**
     * お問い合わせの削除
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json(null, 204);
    }
}
