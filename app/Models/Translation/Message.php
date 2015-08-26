<?php namespace App\Models\Translation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Message extends Model
{
    protected $table = 'translation_messages';

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function scopeLatest(Builder $query)
    {
        return $query;
    }
}
