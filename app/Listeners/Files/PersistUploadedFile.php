<?php namespace App\Listeners\Files;

use App\Events\Files\Uploaded;
use App\Models\File;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        |---------------------------------
        | Check the tag and its limit.
        |---------------------------------
        */

        /** @var \App\Models\User $me */
        $me = app('sentinel')->getUser();
        $tag = $request->get('qqtag');
        $tagLimit = config('filesystems.allowed_tags_and_limits.' . $tag);
        if ($tagLimit > 0) {
            $allFilesWithSameTagBelongingToUser = $me->load([
                'files' => function (BelongsToMany $query) use ($tag) {
                    $query->wherePivot('tag', '=', $tag);
                }
            ])->files;
            if (($numberOfFilesToDeleteToComplyWitTagLimit = $allFilesWithSameTagBelongingToUser->count() - $tagLimit + 1) > 0) {
                while ($numberOfFilesToDeleteToComplyWitTagLimit > 0) {
                    $pivotToDelete = $allFilesWithSameTagBelongingToUser->shift();
                    app('filestream')->deleteFile($pivotToDelete->hash, $tag);
                    $numberOfFilesToDeleteToComplyWitTagLimit--;
                }
            }
        }

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

        $me->files()->newPivotStatement()->whereTag($tag)->whereFileHash($hash)->delete();

        $file->uploaders()->attach([
            $me->getUserId() => [
                'uuid' => $request->get('qquuid'),
                'original_client_name' => $request->get('qqfilename'),
                'tag' => $tag
            ]
        ]);

        return response()->json(['success' => true, 'hash' => $file->hash])->setStatusCode(IlluminateResponse::HTTP_CREATED);
    }
}
