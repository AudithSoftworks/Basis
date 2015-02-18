<?php namespace App\Models;

use \Illuminate\Auth\Authenticatable;
use \Illuminate\Auth\Passwords\CanResetPassword;
use \Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use \Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use \Illuminate\Database\Eloquent\SoftDeletes;

class User extends \Eloquent implements AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable, CanResetPassword, SoftDeletes;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * All fields are 'guarded' (protected against mass-assignment)
     *
     * @var array
     */
    protected $guarded = array('*');

    /**
     * Soft-deletes enabled.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Model uses Unix-timestamp date-format.
     *
     * @return string
     */
    protected function getDateFormat()
    {
        return 'U';
    }
}
