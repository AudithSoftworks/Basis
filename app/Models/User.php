<?php namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\User
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $permissions
 * @property string $last_login
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|UserOAuth[] $linkedAccounts
 * @property-read \Illuminate\Database\Eloquent\Collection|File[] $files
 * @property-read \Illuminate\Database\Eloquent\Collection|\static::$rolesModel[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\static::$persistencesModel[] $persistences
 * @property-read \Illuminate\Database\Eloquent\Collection|\static::$activationsModel[] $activations
 * @property-read \Illuminate\Database\Eloquent\Collection|\static::$remindersModel[] $reminders
 * @property-read \Illuminate\Database\Eloquent\Collection|\static::$throttlingModel[] $throttle
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User wherePermissions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereLastLogin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\User whereDeletedAt($value)
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function linkedAccounts()
    {
        return $this->hasMany(UserOAuth::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany(File::class, 'files_users', 'user_id', 'file_hash')->withTimestamps()->withPivot(['uuid', 'original_client_name']);
    }
}
