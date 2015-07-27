<?php namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'translation_categories';

    protected $dateFormat = 'U';

    public function messages()
    {
        return $this->hasMany(Message::class, 'category_id', 'id');
    }
}
