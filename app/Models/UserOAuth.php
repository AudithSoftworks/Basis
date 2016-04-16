<?php namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserOAuth
 *
 * @mixin \Eloquent
 * @property integer               $id
 * @property integer               $user_id
 * @property string                $remote_provider
 * @property string                $remote_id
 * @property string                $nickname
 * @property string                $name
 * @property string                $email
 * @property string                $avatar
 * @property \Carbon\Carbon        $created_at
 * @property \Carbon\Carbon        $updated_at
 * @property-read \App\Models\User $owner
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserOAuth whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserOAuth whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserOAuth whereRemoteProvider($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserOAuth whereRemoteId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserOAuth whereNickname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserOAuth whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserOAuth whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserOAuth whereAvatar($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserOAuth whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserOAuth whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserOAuth ofProvider($provider)
 */
class UserOAuth extends Model
{
    protected $table = 'users_oauth';

    protected $hidden = [];

    protected $fillable = ['*'];

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @param Builder $query
     * @param string  $provider
     *
     * @return mixed
     */
    public function scopeOfProvider(Builder $query, $provider)
    {
        return $query->where('remote_provider', '=', $provider);
    }
}
