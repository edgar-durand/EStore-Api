<?php

namespace App\Http\Controllers;

use App\Concepto;
use Illuminate\Http\Request;

class ConceptoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(['response' => ['data' => Concepto::all(), 'message' => 'Retrieving all Concepts.'], 'status' => 200, 'error' => null], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $concept = Concepto::whereName(request('name'))->first();
        if ($concept) {
            return response()->json(['response' => ['data' => null, 'message' => 'Concept exists !'], 'status' => 200, 'error' => null], 200);
        } else {
            if (request('name')) {
                Concepto::create([
                    'name' => request('name'),
                    'description' => request('description')
                ]);
                return response()->json(['response' => ['data' => null, 'message' => 'created.'], 'status' => 201, 'error' => null], 201);
            }
            return response()->json(['response' => null, 'status' => 422, 'error' => ['message'=>'Field _name_ required !']], 422);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Concepto $concepto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $concepto)
    {
        $toUpdate = Concepto::find($concepto);
        if ($toUpdate){
            if($request->method() === 'PUT')
            {
                $toUpdate->name = request('name');
                $toUpdate->description = request('description');
                $toUpdate->save();
                return response()->json(['response' => ['data' => null, 'message' => 'Updated !'], 'error' => null, 'status' => 202], 202);
            }
            if ($request->method() === 'PATCH'){
                $flag = false;
                $columns = [];
                if (request('name')){
                    $flag = true;
                    $columns[] = array('name');
                    $toUpdate->name = request('name');
                }
                if (request('description')){
                    $flag = true;
                    $columns[] = array('description');
                    $toUpdate->description = request('description');
                }
                if ($flag){
                    $toUpdate->save();
                    return response()->json(['response' => ['data' => $columns, 'message' => 'Updated !'], 'error' => null, 'status' => 202], 202);
                }
                return response()->json(['response' => ['data' => null, 'message' => 'No data provided, Nothing has changed !'], 'error' => null, 'status' => 200], 200);
            }


        }
        return response()->json(['response' => null, 'error' => ['message' => 'Concept ID ' . $concepto . ' No Found !'], 'status' => 404], 404);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Concepto $concepto
     * @return \Illuminate\Http\Response
     */
    public function destroy( $concepto)
    {
        $toDelete = Concepto::find($concepto);
        if ($toDelete){
            $toDelete->delete();
            return response()->json(['response' => ['data' => null, 'message' => 'Concept is been deleted !'], 'error' => null, 'status' => 200], 200);

        }
        return response()->json(['response' => null, 'error' => ['message' => 'Concept ID ' . $concepto . ' No Found !'], 'status' => 404], 404);

    }
}
