<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::all();
        return $category->count() ?
            response()->json(['response' => ['data' => $category, 'message' => 'Resolving all categories.'], 'status' => 200, 'error' => null], 200) :
            response()->json(['response' => ['data' => null, 'message' => 'No Categories registered.'], 'status' => 200, 'error' => null], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function create()
//    {
//        //
//    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->name) {
            $name = Category::whereName($request->name)->first();
            if ($name) {
                return response()->json(['response' => null, 'error' => ['message' => 'Category exist !'], 'status' => 422], 422);
            } else {
                Category::create([
                    'name' => $request->name,
                    'description' => $request->description
                ]);
                return response()->json(['response' => ['data' => null, 'message' => 'created !'], 'error' => null, 'status' => 201], 201);
            }
        }
        return response()->json(['response' => null, 'error' => ['message' => 'Data is invalid or null !'], 'status' => 422], 422);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Category $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $category)
    {
        $toUpdate = Category::find($category);
        if ($toUpdate){
            if($request->method() === 'PUT')
            {
                $toUpdate->name = $request->name;
                $toUpdate->description = $request->description;
                $toUpdate->save();
                return response()->json(['response' => ['data' => null, 'message' => 'Updated !'], 'error' => null, 'status' => 202], 202);
            }
            if ($request->method() === 'PATCH'){
                $flag = false;
                $columns = [];
                if ($request->name){
                    $flag = true;
                    $columns[] = array('name');
                    $toUpdate->name = $request->name;
                }
                if ($request->description){
                    $flag = true;
                    $columns[] = array('description');
                    $toUpdate->description = $request->description;
                }
                if ($flag){
                    $toUpdate->save();
                    return response()->json(['response' => ['data' => $columns, 'message' => 'Updated !'], 'error' => null, 'status' => 202], 202);
                }
                return response()->json(['response' => ['data' => null, 'message' => 'No data provided, Nothing has changed !'], 'error' => null, 'status' => 200], 200);
            }


        }
        return response()->json(['response' => null, 'error' => ['message' => 'Category ID ' . $category . ' No Found !'], 'status' => 404], 404);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Category $category
     * @return \Illuminate\Http\Response
     */
    public function destroy($category)
    {
        $toDelete = Category::find($category);
        if ($toDelete){
            $toDelete->delete();
            return response()->json(['response' => ['data' => null, 'message' => 'Category is been deleted !'], 'error' => null, 'status' => 200], 200);
        }
        return response()->json(['response' => null, 'error' => ['message' => 'Category ID ' . $category . ' No Found !'], 'status' => 404], 404);
    }
}
