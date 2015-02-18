<?php namespace App\Http\Controllers;

use \App\Models\Category;

class CategoriesController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Response
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return \Response
     */
    public function store()
    {
        $newEntityName = \Input::get('entityName');
        $referenceEntityId = \Input::get('referenceEntityId');
        $methodName = \Input::get('methodName');

        $newCollection = new Category();
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
     * @return \Response
     */
    public function show($id)
    {
        $categories = new Category();

        return $categories->find(intval($id));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Response
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
     * @return \Response
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
     * @return \Response
     */
    public function destroy($id)
    {
        $collection = new Category();

        return $collection->remove(intval($id));
    }
}
