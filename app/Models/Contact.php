<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'first_name',
        'last_name',
        'gender',
        'email',
        'tel',
        'address',
        'building',
        'detail',
    ];

    /**この問い合わせが属するカテゴリーの取得 */

    public function category():belongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** この問い合わせが所有するタグを取得 */

    public function tags():belongsToMany
    {
        return $this -> belongsToMany(Tag::class);
    }

    /** 性別に関するラベルを取得する */

    public function getGenderLabelAttribute():string
    {
        return match ($this->gender){
            1 =>'男性',
            2 =>'女性',
            3 =>'その他',
        };
    }


}
