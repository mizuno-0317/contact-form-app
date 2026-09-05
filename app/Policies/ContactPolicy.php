<?php

namespace App\Policies;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ContactPolicy
{
    /**
     * 問い合わせの表示
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * 問い合わせ詳細の表示
     */
    public function view(User $user, Contact $contact): bool
    {
        return true;
    }


    /**
     * 問い合わせの削除
     */
    public function delete(User $user, Contact $contact): bool
    {
        return true;
    }

    
}
