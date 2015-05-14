<?php namespace App\Models;

use Illuminate\Database\Query\Builder;

/**
 * App\Models\UserOAuth
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $remote_provider
 * @property string $remote_id
 * @property string $nickname
 * @property string $name
 * @property string $email
 * @property string $avatar
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
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
 */
class UserOAuth extends \Eloquent
{
    protected $table = 'users_oauth';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * All fields are 'guarded' (protected against mass-assignment)
     *
     * @var array
     */
    protected $guarded = array('*');

    protected function getDateFormat()
    {
        return 'U';
    }

    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    /**
     * @param Builder $query
     *
     * @return mixed
     */
    public function scopeGoogle($query)
    {
        return $query->where('remote_provider', '=', 'google');
    }
}
