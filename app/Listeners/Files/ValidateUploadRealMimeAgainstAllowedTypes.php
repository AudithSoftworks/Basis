<?php namespace App\Listeners\Files;

use App\Events\Files\Uploaded;
use App\Exceptions\FileStream as FileStreamExceptions;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class ValidateUploadRealMimeAgainstAllowedTypes
{
    /**
     * @param \App\Events\Files\Uploaded $event
     *
     * @return bool
     */
    public function handle(Uploaded $event)
    {
        $uploadUuid = $event->uploadUuid;
        $file = new SymfonyFile($path = app('filestream')->getAbsolutePath($uploadUuid));
        if ($allowedMimeTypes = config('filesystems.allowed_mimetypes')) {
            if (is_array($allowedMimeTypes) && !is_null($fileMimeType = $file->getMimeType())) {
                if (!in_array($fileMimeType, $allowedMimeTypes)) {
                    unlink($path);
                    throw new FileStreamExceptions\MimeTypeNotAllowedException;
                }
            }
        }

        return true;
    }
}
