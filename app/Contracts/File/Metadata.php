<?php namespace App\Contracts\File;

abstract class Metadata
{
    /**
     * File id.
     *
     * @var int
     */
    public $id;

    /**
     * File Md5-hash string.
     *
     * @var string
     */
    public $hash;

    /**
     * File MIME-type.
     *
     * @var string
     */
    public $mime = 'application/octet-stream';

    /**
     * Additional metadata info.
     *
     * @var string
     */
    public $metadata = '';

    /**
     * File creation time in Unix-epoch style.
     *
     * @var \Carbon\Carbon|null
     */
    public $created_at;

    /**
     * File modification time in Unix-epoch style.
     *
     * @var \Carbon\Carbon|null
     */
    public $updated_at;

    /**
     * File soft-deletion time in Unix-epoch style.
     *
     * @var \Carbon\Carbon|null
     */
    public $deleted_at;
}
