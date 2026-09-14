<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExportContactRequest;
use App\Http\Requests\StoreContactRequest;
use App\Models\Category;
use App\Models\Contact;
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

        return view('contact.index', compact('categories', 'tags'));
    }

    /**
     * 確認画面の表示
     */
    public function confirm(StoreContactRequest $request)
    {
        $validated = $request->validated();
        $category = Category::find($validated['category_id']);
        $tags = Tag::whereIn('id', $validated['tag_ids'])->get();

        return view('contact.confirm', compact('validated', 'category', 'tags'));
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

    public function export(ExportContactRequest $request)
    {
        $query = Contact::query()->with('category');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $contacts = $query->latest()->get();

        return response()->streamDownload(function () use ($contacts) {
            $handle = fopen('php://output', 'w');

            // BOMをつける
            fwrite($handle, "\xEF\xBB\xBF");

            // ヘッダー
            fputcsv($handle, [
                'ID',
                '氏名',
                '性別',
                'メール',
                '電話',
                '住所',
                '建物',
                'カテゴリ',
                '内容',
                '作成日時',
            ]);

            foreach ($contacts as $contact) {
                fputcsv($handle, [
                    $contact->id,
                    $contact->first_name.' '.$contact->last_name,
                    $contact->gender_label,
                    $contact->email,
                    $contact->tel,
                    $contact->address,
                    $contact->building,
                    $contact->category->content,
                    $contact->detail,
                    $contact->created_at,
                ]);
            }

            fclose($handle);
        }, 'contacts.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}
