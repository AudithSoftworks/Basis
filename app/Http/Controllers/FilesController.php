<?php namespace App\Http\Controllers;

class FilesController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $newEntityName = Input::get('entityName');
        $referenceEntityId = Input::get('referenceEntityId');
        $methodName = Input::get('methodName');

        $newCollection = new Categories();
        if (!method_exists($newCollection, $methodName)) {
            throw new \BadMethodCallException("Method [" . $methodName . "] not found!");
        }

        return $newCollection->$methodName($newEntityName, $referenceEntityId);
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

        return Cache::rememberForever('file-' . $id, function ($id) {
            return Files::where(is_numeric($id) ? 'id' : 'hash', '=', $id)->get();
        });
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
        //
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $collection = new Categories();

        return $collection->remove(intval($id));
    }
}
