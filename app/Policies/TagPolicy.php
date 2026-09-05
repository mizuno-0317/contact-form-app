<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TagPolicy
{
    /**
     * タグの一覧表示
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * タグの詳細表示
     */
    public function view(User $user, Tag $tag): bool
    {
        return true;
    }

    /**
     * タグの作成
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * タグの編集
     */
    public function update(User $user, Tag $tag): bool
    {
        return true;
    }

    /**
     * タグの削除
     */
    public function delete(User $user, Tag $tag): bool
    {
        return true;
    }


}
