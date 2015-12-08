<?php namespace App\Events\Files;

use App\Events\Event as EventAbstract;
use Illuminate\Http\Request;

class Uploaded extends EventAbstract
{
    /**
     * @var string
     */
    public $uploadUuid;

    /**
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * @param string                   $uploadUuid
     * @param \Illuminate\Http\Request $request
     */
    public function __construct($uploadUuid, Request $request)
    {
        $this->uploadUuid = $uploadUuid;
        $this->request = $request;

        parent::__construct();
    }
}
