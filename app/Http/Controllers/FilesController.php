<?php namespace App\Http\Controllers;

use \Audith\Contracts\File,
    \Response;

class FilesController extends Controller
{
    /**
     * @var File
     */
    public $file;

    /**
     * @param File $file
     */
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    /**
     *
     */
    public function index()
    {
        ob_start();
        session_id($_COOKIE["PHPSESSID"]);
        session_start();
        $key = ini_get("session.upload_progress.prefix") . "myForm";
        $session = $_SESSION;
        $session2 = \Session::all();
        echo Response::json($_SESSION);
        return ob_get_clean();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        session_id($_COOKIE["PHPSESSID"]);
        session_start();

        return \View::make("/file/create");
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        session_id($_COOKIE["PHPSESSID"]);
        session_start();


        /*

        $contents = "test";

        return $this->file->post($contents, File::VISIBILITY_PUBLIC);

        */
    }


    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $id = preg_match('/^[a-z0-9]{32}$/i', $id) ? strtolower($id) : intval($id);

        return $this->file->get($id);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function update($id)
    {
        $id = preg_match('/^[a-z0-9]{32}$/i', $id) ? strtolower($id) : intval($id);
        $contents = "test";

        return $this->file->put($id, $contents, File::VISIBILITY_PUBLIC);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param integer[]|string[] $ids Collection of numeric ids or Md5-hashes of the files to be deleted.
     *
     * @return Response
     */
    public function destroy(array $ids)
    {
        return $this->file->delete($ids);
    }
}
