<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\UserActivation
 *
 * @mixin \Eloquent
 * @property integer        $id
 * @property integer        $user_id
 * @property string         $code
 * @property boolean        $completed
 * @property string         $completed_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserActivation whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserActivation whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserActivation whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserActivation whereCompleted($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserActivation whereCompletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserActivation whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\UserActivation whereUpdatedAt($value)
 */
class UserActivation extends Model
{
    protected $table = 'users_activations';

    protected $fillable = [
        'code',
        'completed',
        'completed_at',
    ];

    /**
     * @param  mixed $completed
     *
     * @return bool
     */
    public function getCompleted($completed)
    {
        return (bool)$completed;
    }

    /**
     * @param  mixed $completed
     *
     * @return void
     */
    public function setCompleted($completed)
    {
        $this->attributes['completed'] = (bool)$completed;
    }

    public function getCode()
    {
        return $this->attributes['code'];
    }
}
