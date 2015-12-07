<?php namespace App\Listeners\Files;

use App\Events\Files\Uploaded;
use App\Models\File;
use Illuminate\Http\Response as IlluminateResponse;
use Symfony\Component\HttpFoundation\File\File as SymfonyFile;

class PersistUploadedFile
{
    /**
     * @param \App\Events\Files\Uploaded $event
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function handle(Uploaded $event)
    {
        $uploadUuid = $event->uploadUuid;
        $request = $event->request;

        /*
        |--------------------------------------------------
        | Move file to its final permanent destination.
        |--------------------------------------------------
        */

        $hash = hash_file('sha256', app('filestream')->getAbsolutePath($uploadUuid));
        $destination = $hash;
        if (config('filesystems.load_balancing.enabled')) {
            $config = config('filesystems.load_balancing');
            $folders = [];
            for ($i = 0; $i < $config['depth']; $i++) {
                $folders[] = substr($hash, -1 * ($i + 1) * $config['length'], $config['length']);
            }
            $destination = implode(DIRECTORY_SEPARATOR, array_merge($folders, [$hash]));
        }
        $filesystem = app('filesystem')->disk();
        (!$filesystem->exists($destination)) ? $filesystem->move($uploadUuid, $destination) : $filesystem->delete($uploadUuid);

        /*
        |------------------------------------
        | Persist file record in database.
        |------------------------------------
        */

        $uploadedFile = new SymfonyFile(app('filestream')->getAbsolutePath($destination));
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

        return response()->json(['success' => true, 'uuid' => $uploadUuid])->setStatusCode(IlluminateResponse::HTTP_CREATED);
    }
}
