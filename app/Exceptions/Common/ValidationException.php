<?php namespace App\Exceptions\Common;

use Illuminate\Validation\ValidationException as IlluminateValidationException;

class ValidationException extends IlluminateValidationException
{
    /**
     * ValidationException constructor.
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    public function __construct(\Illuminate\Validation\Validator $validator)
    {
        parent::__construct($validator);
    }
}
