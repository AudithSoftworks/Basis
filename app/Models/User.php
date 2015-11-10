<?php namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\User
 *
 * @mixin \Eloquent
 * @property integer                                                     $id
 * @property string                                                      $name
 * @property string                                                      $email
 * @property string                                                      $password
 * @property string                                                      $permissions
 * @property string                                                      $last_login
 * @property \Carbon\Carbon                                              $created_at
 * @property \Carbon\Carbon                                              $updated_at
 * @property \Carbon\Carbon                                              $deleted_at
 * @property-read \App\Models\UserOAuth[]                                $linkedAccounts
 * @property-read \Cartalyst\Sentinel\Roles\EloquentRole[]               $roles
 * @property-read \Cartalyst\Sentinel\Persistences\EloquentPersistence[] $persistences
 * @property-read \Cartalyst\Sentinel\Activations\EloquentActivation[]   $activations
 * @property-read \Cartalyst\Sentinel\Reminders\EloquentReminder[]       $reminders
 * @property-read \Cartalyst\Sentinel\Throttling\EloquentThrottle[]      $throttle
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User wherePermissions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereLastLogin($value)
 */
class User extends EloquentUser
{
    use SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password'];

    /**
     * {@inheritDoc}
     */
    protected $fillable = [
        'email',
        'password',
        'name',
        'permissions',
    ];

    /**
     * Soft-deletes enabled.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function linkedAccounts()
    {
        return $this->hasMany(UserOAuth::class, 'user_id', 'id');
    }
}
