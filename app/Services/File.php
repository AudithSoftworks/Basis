<?php namespace App\Services;

use App\Contracts\File as FileContract;
use App\Exceptions\File as FileExceptions;

class File implements FileContract
{
    /**
     * @param integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return boolean
     */
    public function exists($id)
    {
        //
    }

    /**
     * Get the metadata information of the file.
     *
     * @param integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return FileContract\Metadata
     */
    public function getMetadata($id)
    {
        //
    }

    /**
     * Get the contents of the file.
     *
     * @param integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return string
     * @throws FileContract\Exception\FileNotFoundException
     */
    public function get($id)
    {
        return "test";
    }

    /**
     * Create a new file record and writes its content.
     *
     * @param string      $contents   File contents.
     * @param string|null $visibility File visibility setting.
     *
     * @return boolean
     * @throws FileExceptions\SessionUploadProgress\NotEnabledException
     * @throws FileExceptions\SessionUploadProgress\NameNotSetException
     */
    public function post($contents, $visibility = self::VISIBILITY_PRIVATE)
    {
        if (!ini_get('session.upload_progress.enabled')) {
            throw new FileExceptions\SessionUploadProgress\NotEnabledException();
        }
        if (!($sessionUploadProgressName = ini_get('session.upload_progress.name'))) {
            throw new FileExceptions\SessionUploadProgress\NameNotSetException();
        }
    }

    /**
     * Update the file with new content and (optionally) visibility setting.
     *
     * @param integer|string $id         Numeric id or Md5-hash of the file.
     * @param string         $contents   File contents.
     * @param string|null    $visibility File visibility setting.
     *
     * @return boolean
     */
    public function put($id, $contents, $visibility = self::VISIBILITY_PRIVATE)
    {
        //
    }

    /**
     * Get the file visibility setting.
     *
     * @param  integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return string
     */
    public function getVisibility($id)
    {
        //
    }

    /**
     * Set new file visibility setting to the file.
     *
     * @param integer|string $id         Numeric id or Md5-hash of the file.
     * @param string         $visibility File visibility setting.
     *
     * @return boolean
     */
    public function setVisibility($id, $visibility = self::VISIBILITY_PRIVATE)
    {

    }

    /**
     * Soft-delete selected files.
     *
     * @param integer[]|string[] $ids Collection of numeric ids or Md5-hashes of the files to be deleted.
     *
     * @return boolean
     */
    public function delete($ids)
    {
        //
    }

    /**
     * Get the file size in bytes.
     *
     * @param integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return integer
     */
    public function size($id)
    {
        //
    }

    /**
     * Get the file modification time.
     *
     * @param integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return \Carbon\Carbon
     */
    public function lastModified($id)
    {
        //
    }
}
