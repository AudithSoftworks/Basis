<?php namespace App\Exceptions\Common;

use Illuminate\Validation\Validator;

class ValidationException extends \RuntimeException
{
    public function __construct(Validator $validator)
    {
        if ($validator->fails()) {
            parent::__construct(json_encode($validator->errors()->getMessages()));
        }
    }
}
