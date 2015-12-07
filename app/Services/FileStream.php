<?php namespace App\Services;

use App\Exceptions\FileStream as FileStreamExceptions;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileStream
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    public $filesystem;

    /**
     * Folder to hold uploaded chunks.
     *
     * @var string
     */
    public $temporaryChunksFolder;

    /**
     * Chunks will be cleaned once in 1000 requests on average.
     *
     * @var float
     */
    public $chunksCleanupProbability = 0.001;

    /**
     * By default, chunks are considered loose and deletable, in 1 week.
     *
     * @var int
     */
    public $chunksExpireIn = 604800;

    /**
     * Upload size limit.
     *
     * @var int
     */
    public $sizeLimit;

    public function __construct()
    {
        $this->filesystem = app('filesystem')->disk();
        $this->temporaryChunksFolder = DIRECTORY_SEPARATOR . '_chunks';
        if (app('config')->has('filesystems.chunks_ttl') && is_int(config('filesystems.chunks_ttl'))) {
            $this->chunksExpireIn = config('filesystems.chunks_ttl');
        }
        if (app('config')->has('filesystems.size_limit') && is_int(config('filesystems.size_limit'))) {
            $this->sizeLimit = config('filesystems.size_limit');
        }
    }

    /**
     * Write the uploaded file to the local filesystem.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleUpload(Request $request)
    {
        $fineUploaderUuid = null;
        if ($request->has('qquuid')) {
            $fineUploaderUuid = $request->get('qquuid');
        }

        //------------------------------
        // Is it Post-processing?
        //------------------------------

        if ($request->has('post-process') && $request->get('post-process') == 1) {
            # Combine chunks.
            $this->combineChunks($request);

            return $this->postProcess($request);
        }

        //----------------
        // Prelim work.
        //----------------

        $filesystem = app('filesystem')->disk();

        if (!file_exists($this->temporaryChunksFolder) || !is_dir($this->temporaryChunksFolder)) {
            $filesystem->makeDirectory($this->temporaryChunksFolder);
        }

        # Temp folder writable?
        if (!is_writable($absolutePathToTemporaryChunksFolder = config('filesystems.disks.local.root') . $this->temporaryChunksFolder) || !is_executable($absolutePathToTemporaryChunksFolder)) {
            throw new FileStreamExceptions\TemporaryUploadFolderNotWritableException;
        }

        # Cleanup chunks.
        if (1 === mt_rand(1, 1 / $this->chunksCleanupProbability)) {
            $this->cleanupChunks();
        }

        # Check upload size against the size-limit, if any.
        if (!empty($this->sizeLimit)) {
            $uploadIsTooLarge = false;
            $request->has('qqtotalfilesize') && intval($request->get('qqtotalfilesize')) > $this->sizeLimit && $uploadIsTooLarge = true;
            $this->filesizeFromHumanReadableToBytes(ini_get('post_max_size')) < $this->sizeLimit && $uploadIsTooLarge = true;
            $this->filesizeFromHumanReadableToBytes(ini_get('upload_max_filesize')) < $this->sizeLimit && $uploadIsTooLarge = true;
            if ($uploadIsTooLarge) {
                throw new FileStreamExceptions\UploadTooLargeException;
            }
        }

        # Is there attempt for multiple file uploads?
        $collectionOfUploadedFiles = collect($request->file());
        if ($collectionOfUploadedFiles->count() > 1) {
            throw new FileStreamExceptions\MultipleSimultaneousUploadsNotAllowedException;
        }

        /** @var UploadedFile $file */
        $file = $collectionOfUploadedFiles->first();

        //--------------------
        // Upload handling.
        //--------------------

        if ($file->getSize() == 0) {
            throw new FileStreamExceptions\UploadIsEmptyException;
        }

        $name = $file->getClientOriginalName();
        if ($request->has('qqfilename')) {
            $name = $request->get('qqfilename');
        }
        if (empty($name)) {
            throw new FileStreamExceptions\UploadFilenameIsEmptyException;
        }

        $totalNumberOfChunks = $request->has('qqtotalparts') ? $request->get('qqtotalparts') : 1;

        if ($totalNumberOfChunks > 1) {
            $chunkIndex = intval($request->get('qqpartindex'));
            $targetFolder = $this->temporaryChunksFolder . DIRECTORY_SEPARATOR . $fineUploaderUuid;
            if (!$filesystem->exists($targetFolder)) {
                $filesystem->makeDirectory($targetFolder);
            }

            if (!$file->isValid()) {
                throw new FileStreamExceptions\UploadAttemptFailedException;
            }
            $file->move(storage_path('app' . $targetFolder), $chunkIndex);

            return response()->json(['success' => true, 'uuid' => $fineUploaderUuid]);
        } else {
            if (!$file->isValid()) {
                throw new FileStreamExceptions\UploadAttemptFailedException;
            }
            $file->move(storage_path('app'), $fineUploaderUuid);

            return $this->postProcess($request);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function isUploadResumable(Request $request)
    {
        $filesystem = app('filesystem')->disk();
        $fineUploaderUuid = $request->get('qquuid');
        $chunkIndex = intval($request->get('qqpartindex'));
        $numberOfExistingChunks = count($filesystem->files($this->temporaryChunksFolder . DIRECTORY_SEPARATOR . $fineUploaderUuid));
        if ($numberOfExistingChunks < $chunkIndex) {
            throw new FileStreamExceptions\UploadIncompleteException;
        }

        return true;
    }

    /**
     * @param string $size
     *
     * @return false|string
     */
    public function filesizeFromHumanReadableToBytes($size)
    {
        if (preg_match('/^([\d,.]+)\s?([kmgtpezy]?i?b)$/i', $size, $matches) !== 1) {
            return false;
        }
        $coefficient = $matches[1];
        $prefix = strtolower($matches[2]);

        $binaryPrefices = ['b', 'kib', 'mib', 'gib', 'tib', 'pib', 'eib', 'zib', 'yib'];
        $decimalPrefices = ['b', 'kb', 'mb', 'gb', 'tb', 'pb', 'eb', 'zb', 'yb'];

        $base = in_array($prefix, $binaryPrefices) ? 1024 : 1000;
        $flippedPrefixMap = $base == 1024 ? array_flip($binaryPrefices) : array_flip($decimalPrefices);
        $factor = array_pull($flippedPrefixMap, $prefix);

        return sprintf("%d", bcmul(str_replace(',', '', $coefficient), bcpow($base, $factor)));
    }

    /**
     * @param int  $bytes
     * @param int  $decimals
     * @param bool $binary
     *
     * @return string
     */
    public function filesizeFromBytesToHumanReadable($bytes, $decimals = 2, $binary = true)
    {
        $binaryPrefices = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'];
        $decimalPrefices = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = intval(floor((strlen($bytes) - 1) / 3));

        return sprintf("%.{$decimals}f", $bytes / pow($binary ? 1024 : 1000, $factor)) . ' ' . $binary ? $binaryPrefices[$factor] : $decimalPrefices[$factor];
    }

    /**
     * @param string $path
     *
     * @return string
     * @throws \App\Exceptions\FileStream\NotFoundException
     */
    private function getAbsolutePath($path)
    {
        return config('filesystems.disks.local.root') . DIRECTORY_SEPARATOR . trim($path, DIRECTORY_SEPARATOR);
    }

    private function cleanupChunks()
    {
        $filesystem = app('filesystem')->disk('local');
        foreach ($filesystem->directories($this->temporaryChunksFolder) as $file) {
            if (time() - $filesystem->lastModified($file) > $this->chunksExpireIn) {
                $filesystem->deleteDirectory($file);
            }
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function postProcess(Request $request)
    {
        $fineUploaderUuid = null;
        if ($request->has('qquuid')) {
            $fineUploaderUuid = $request->get('qquuid');
        }

        # Real MIME validation of the uploaded file.
        $this->validateUploadRealMimeAgainstAllowedTypes($this->getAbsolutePath($fineUploaderUuid));

        # Move file to its final permanent destination.
        $hash = hash_file('sha256', $this->getAbsolutePath($fineUploaderUuid));
        $destination = $this->renameAndMoveUploadedFileByItsHash($fineUploaderUuid, $hash);

        # Persist file record in database.
        $this->persistDatabaseRecord(new SymfonyFile($this->getAbsolutePath($destination)), $request);

        return response()->json(['message' => 'Created', 'success' => true, 'uuid' => $fineUploaderUuid])->setStatusCode(IlluminateResponse::HTTP_CREATED);
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    private function combineChunks(Request $request)
    {
        # Prelim
        $filesystem = app('filesystem')->disk();
        $fineUploaderUuid = $request->get('qquuid');
        $chunksFolder = $this->temporaryChunksFolder . DIRECTORY_SEPARATOR . $fineUploaderUuid;
        $totalNumberOfChunks = $request->has('qqtotalparts') ? intval($request->get('qqtotalparts')) : 1;

        # Do we have all chunks?
        $numberOfExistingChunks = count($filesystem->files($chunksFolder));
        if ($numberOfExistingChunks != $totalNumberOfChunks) {
            throw new FileStreamExceptions\UploadIncompleteException;
        }

        # We have all chunks, proceed with combine.
        $targetStream = fopen($this->getAbsolutePath($fineUploaderUuid), 'wb');
        for ($i = 0; $i < $totalNumberOfChunks; $i++) {
            $chunkStream = fopen($this->getAbsolutePath($chunksFolder . DIRECTORY_SEPARATOR . $i), 'rb');
            stream_copy_to_stream($chunkStream, $targetStream);
            fclose($chunkStream);
        }
        fclose($targetStream);
        $filesystem->deleteDirectory($chunksFolder);
    }

    /**
     * @param string $path
     *
     * @return void
     */
    private function validateUploadRealMimeAgainstAllowedTypes($path)
    {
        $file = new SymfonyFile($path);
        if ($allowedMimeTypes = config('filesystems.allowed_mimetypes')) {
            if (is_array($allowedMimeTypes) && !is_null($fileMimeType = $file->getMimeType())) {
                if (!in_array($fileMimeType, $allowedMimeTypes)) {
                    unlink($path);
                    throw new FileStreamExceptions\MimeTypeNotAllowedException;
                }
            }
        }
    }

    /**
     * @param string $originalPath
     * @param string $hash
     * @param bool   $loadBalance
     *
     * @return string
     */
    private function renameAndMoveUploadedFileByItsHash($originalPath, $hash, $loadBalance = true)
    {
        $destination = $hash;
        if ($loadBalance) {
            $config = config('filesystems.load_balancing');
            $folders = [];
            for ($i = 0; $i < $config['depth']; $i++) {
                $folders[] = substr($hash, -1 * ($i + 1) * $config['length'], $config['length']);
            }
            $destination = implode(DIRECTORY_SEPARATOR, array_merge($folders, [$hash]));
        }
        $filesystem = app('filesystem')->disk();
        (!$filesystem->exists($destination)) ? $filesystem->move($originalPath, $destination) : $filesystem->delete($originalPath);

        return $destination;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\File $uploadedFile
     * @param \Illuminate\Http\Request                    $request
     */
    private function persistDatabaseRecord(SymfonyFile $uploadedFile, Request $request)
    {
        if (!$file = File::find($hash = $uploadedFile->getFilename())) {
            $file = new File();
            $file->hash = $hash;
            $file->disk = 'local';
            $file->path = trim(str_replace(config('filesystems.disks.local.root'), '', $uploadedFile->getPathname()), DIRECTORY_SEPARATOR);
            $file->mime = $uploadedFile->getMimeType();
            $file->size = $uploadedFile->getSize();
            $file->save();
        }

        /** @var \App\Models\User $user */
        $user = app('sentinel')->getUser();
        if ($duplicate = $user->files()->find($file->hash)) {
            $user->files()->detach($duplicate->hash);
        }

        $file->uploaders()->attach([$user->getUserId() => [
            'uuid' => $request->get('qquuid'),
            'original_client_name' => $request->get('qqfilename')
        ]]);
    }
}
