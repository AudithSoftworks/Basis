<?php namespace App\Traits\Users;

use App\Models\User;
use App\Models\UserActivation;
use Carbon\Carbon;

trait Activates
{
    /**
     * @param \App\Models\User $user
     *
     * @return \App\Models\UserActivation
     */
    protected function create(User $user)
    {
        $activation = new UserActivation();
        $code = $this->generateActivationCode();
        $activation->fill(compact('code'));
        $activation->user_id = $user->id;
        $activation->save();

        return $activation;
    }

    /**
     * @param \App\Models\User $user
     * @param string|null      $code
     *
     * @return bool
     */
    protected function exists(User $user, $code = null)
    {
        $expires = $this->expires();
        $activation = UserActivation::where('user_id', $user->id)->where('completed', false)->where('created_at', '>', $expires);
        if ($code) {
            $activation->where('code', $code);
        }

        return $activation->first() ? true : false;
    }

    /**
     * Returns the expiration date.
     *
     * @return \Carbon\Carbon
     */
    protected function expires()
    {
        $expires = config('auth.activations.expires', 259200);

        return Carbon::now()->subSeconds($expires);
    }

    /**
     * @param \App\Models\User $user
     *
     * @return bool
     */
    protected function completed(User $user)
    {
        $activation = UserActivation::where('user_id', $user->id)->where('completed', true)->first();

        return $activation ? true : false;
    }

    /**
     * @param \App\Models\User $user
     * @param string           $code
     *
     * @return bool
     */
    public function complete(User $user, $code)
    {
        $expires = $this->expires();
        $activation = UserActivation::where('user_id', $user->id)->where('code', $code)->where('completed', false)->where('created_at', '>', $expires)->first();
        if ($activation === null) {
            return false;
        }
        $activation->fill([
            'completed' => true,
            'completed_at' => Carbon::now(),
        ]);
        $activation->save();

        return true;
    }

    /**
     * Return a random string for an activation code.
     *
     * @return string
     */
    protected function generateActivationCode()
    {
        return str_random(32);
    }

    /**
     * Get the e-mail subject line to be used for the reset link email.
     *
     * @return string
     */
    protected function getActivationEmailSubject()
    {
        return trans('auth.activation_email_subject');
    }
}
