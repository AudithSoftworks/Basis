<?php namespace App\Contracts;

use App\Exceptions\File\FileNotFoundException;

/**
 * @author Shahriyar Imanov <shehi@imanov.me>
 */
interface File
{
    /**
     * The public visibility setting.
     *
     * @var string
     */
    const VISIBILITY_PUBLIC = 'public';

    /**
     * The private visibility setting.
     *
     * @var string
     */
    const VISIBILITY_PRIVATE = 'private';

    /**
     * @param integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return boolean
     */
    public function exists($id);

    /**
     * Get the metadata information of the file.
     *
     * @param integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return File\Metadata
     */
    public function getMetadata($id);

    /**
     * Get the contents of the file.
     *
     * @param integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return string
     * @throws FileNotFoundException
     */
    public function get($id);

    /**
     * Create a new file record and writes its content.
     *
     * @param string      $contents   File contents.
     * @param string|null $visibility File visibility setting.
     *
     * @return boolean
     */
    public function post($contents, $visibility = self::VISIBILITY_PRIVATE);

    /**
     * Update the file with new content and (optionally) visibility setting.
     *
     * @param integer|string $id         Numeric id or Md5-hash of the file.
     * @param string         $contents   File contents.
     * @param string|null    $visibility File visibility setting.
     *
     * @return boolean
     */
    public function put($id, $contents, $visibility = self::VISIBILITY_PRIVATE);

    /**
     * Get the file visibility setting.
     *
     * @param  integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return string
     */
    public function getVisibility($id);

    /**
     * Set new file visibility setting to the file.
     *
     * @param integer|string $id         Numeric id or Md5-hash of the file.
     * @param string         $visibility File visibility setting.
     *
     * @return boolean
     */
    public function setVisibility($id, $visibility = self::VISIBILITY_PRIVATE);

    /**
     * Soft-delete selected files.
     *
     * @param integer[]|string[] $ids Collection of numeric ids or Md5-hashes of the files to be deleted.
     *
     * @return boolean
     */
    public function delete($ids);

    /**
     * Get the file size in bytes.
     *
     * @param integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return integer
     */
    public function size($id);

    /**
     * Get the file modification time.
     *
     * @param integer|string $id Numeric id or Md5-hash of the file.
     *
     * @return \Carbon\Carbon
     */
    public function lastModified($id);
}
