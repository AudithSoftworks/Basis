<?php namespace App\Http\Controllers;

use App\Exceptions\Common\ValidationException;
use App\Exceptions\FileStream as FileStreamExceptions;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;

class FilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        /** @var \App\Models\User $me */
        $me = app('sentinel')->getUser();

        return response()->json($me->files->toArray());
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('file.create');
    }


    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = app('validator')->make($request->all(), [
            'qquuid' => 'required|string|size:36',
            'qqfilename' => 'required|string',
            'qqtotalfilesize' => 'required|numeric',
            'qqtag' => 'required|string|in:' . implode(',', array_keys(config('filesystems.allowed_tags_and_limits'))),
            'qqtotalparts' => 'required_with_all:qqpartindex,qqpartbyteoffset,qqchunksize|numeric',
            'qqpartindex' => 'required_with_all:qqtotalparts,qqpartbyteoffset,qqchunksize|numeric',
            'qqpartbyteoffset' => 'required_with_all:qqpartindex,qqtotalparts,qqchunksize|numeric',
            'qqchunksize' => 'required_with_all:qqpartindex,qqpartbyteoffset,qqtotalparts|numeric',
            'qqresume' => 'sometimes|required_with_all:qqpartindex,qqpartbyteoffset,qqtotalparts,qqchunksize|string'
        ]);
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (strpos($request->header('content-type'), 'multipart/form-data') === false && !$request->has('post-process')) {
            throw new FileStreamExceptions\UploadRequestIsNotMultipartFormDataException;
        }

        $request->has('qqresume') && $request->get('qqresume') === 'true' && app('filestream')->isUploadResumable($request);

        return app('filestream')->handleUpload($request);
    }


    /**
     * @param string $hash
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function show($hash)
    {
        /** @var \App\Models\User $me */
        $me = app('sentinel')->getUser();

        $file = $me->with(['files' => function (BelongsToMany $query) use ($hash) {
            $query->where('hash', '=', $hash);
        }])->first()->files->first();


        return response()->json($file);
    }


    /**
     * @param string $hash
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($hash)
    {
        app('filestream')->deleteFile($hash);

        return response()->json()->setStatusCode(IlluminateResponse::HTTP_NO_CONTENT);
    }
}
