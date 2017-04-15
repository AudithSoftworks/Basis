<?php namespace App\Models;

use App\Traits\Users\CanActivate;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * App\Models\User
 *
 * @mixin \Eloquent
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|UserOAuth[] $linkedAccounts
 * @property-read \Illuminate\Database\Eloquent\Collection|File[] $files
 * @method static \Illuminate\Database\Query\Builder|User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User whereDeletedAt($value)
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens, CanActivate, CanResetPassword, SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

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
