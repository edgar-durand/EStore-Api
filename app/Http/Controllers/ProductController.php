<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Product::all()->where('_public', true);
        return $product->count() ?
            response()->json(['response' => ['data' => $product, 'message' => 'Resolving all products.'], 'status' => 200, 'error' => null], 200) :
            response()->json(['response' => ['data' => null, 'message' => 'No Products stored.'], 'status' => 200, 'error' => null], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->name && $request->user_id && $request->category_id && $request->price_cost) {
            $name = Product::whereName($request->name)->where('price_cost', $request->price_cost)->first();
            if ($name) {
                return response()->json(['response' => null, 'error' => ['message' => 'This Product name and Price already exist !'], 'status' => 422], 422);
            } else {
                Product::create([
                    'user_id' => $request->user_id,
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'image' => $request->image,
                    'description' => $request->description,
                    'price_cost' => $request->price_cost,
                    'inStock' => $request->inStock,
                    '_public' => $request->_public
                ])->withTimeStamp();
                return response()->json(['response' => ['data' => null, 'message' => 'created !'], 'error' => null, 'status' => 201], 201);
            }
        }
        return response()->json(['response' => null, 'error' => ['message' => 'Data is invalid or null !'], 'status' => 422], 422);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Category $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        return $product ?
            response()->json(['response' => ['data' => $product, 'message' => 'Resolving product for ID: ' . $id . ''], 'error' => null, 'status' => 200], 200) :
            response()->json(['response' => null, 'error' => ['message' => 'ID ' . $id . ' No Found !'], 'status' => 404], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Category $product
     * @return \Illuminate\Http\Response
     */
    public function myProducts()
    {
        $products = Product::all()->where('user_id', auth()->id())->flatten();
        return $products->count() ?
            response()->json(['response' => ['data' => $products, 'message' => 'Your products delivered.'], 'error' => null, 'status' => 200], 200) :
            response()->json(['response' => ['data' => null, 'message' => 'You have no products'], 'error' => null, 'status' => 200], 200);

    }

    public function update(Request $request, $product)
    {
        $toUpdate = Product::find($product);
        if ($toUpdate) {
            if ($toUpdate->user_id == auth()->id()) {
                if ($request->method() === 'PUT') {
                    $toUpdate->user_id = $request->user_id;
                    $toUpdate->category_id = $request->category_id;
                    $toUpdate->image = $request->image;
                    $toUpdate->price_cost = $request->price_cost;
                    $toUpdate->inStock = $request->inStock;
                    $toUpdate->_public = $request->_public;
                    $toUpdate->name = $request->name;
                    $toUpdate->description = $request->description;
                    $toUpdate->updated_at = now();
                    $toUpdate->save();
                    return response()->json(['response' => ['data' => null, 'message' => 'Updated !'], 'error' => null, 'status' => 202], 202);
                }
                if ($request->method() === 'PATCH') {
                    $flag = false;
                    $columns = [];

                    if ($request->_public) {
                        $flag = true;
                        $columns[] = array('_public');
                        $toUpdate->_public = $request->_public;
                    }
                    if ($request->inStock) {
                        $flag = true;
                        $columns[] = array('inStock');
                        $toUpdate->inStock = $request->inStock;
                    }
                    if ($request->price_cost) {
                        $flag = true;
                        $columns[] = array('price_cost');
                        $toUpdate->price_cost = $request->price_cost;
                    }
                    if ($request->image) {
                        $flag = true;
                        $columns[] = array('image');
                        $toUpdate->image = $request->image;
                    }
                    if ($request->category_id) {
                        $flag = true;
                        $columns[] = array('category_id');
                        $toUpdate->category_id = $request->category_id;
                    }
                    if ($request->name) {
                        $flag = true;
                        $columns[] = array('name');
                        $toUpdate->name = $request->name;
                    }
                    if ($request->user_id) {
                        $flag = true;
                        $columns[] = array('user_id');
                        $toUpdate->user_id = $request->user_id;
                    }
                    if ($request->description) {
                        $flag = true;
                        $columns[] = array('description');
                        $toUpdate->description = $request->description;
                    }
                    if ($flag) {
                        $toUpdate->updated_at = now();
                        $toUpdate->save();
                        return response()->json(['response' => ['data' => $columns, 'message' => 'Updated !'], 'error' => null, 'status' => 202], 202);
                    }
                    return response()->json(['response' => ['data' => null, 'message' => 'No data provided, Nothing has changed !'], 'error' => null, 'status' => 200], 200);
                }
            }
            return response()->json(['response' => null, 'error' => ['message' => 'Product ID ' . $product . ' does not belongs to you !'], 'status' => 401], 401);

        }
        return response()->json(['response' => null, 'error' => ['message' => 'Product ID ' . $product . ' No Found !'], 'status' => 404], 404);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Category $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($product)
    {
        $toDelete = Product::find($product);
        if ($toDelete) {
            if ($toDelete->user_id == auth()->id()) {
                $toDelete->delete();
                return response()->json(['response' => ['data' => null, 'message' => 'Product is been deleted !'], 'error' => null, 'status' => 200], 200);
            }
            return response()->json(['response' => null, 'error' => ['message' => 'Product ID ' . $product . ' does not belongs to you !'], 'status' => 401], 401);
        }
        return response()->json(['response' => null, 'error' => ['message' => 'Category ID ' . $product . ' No Found !'], 'status' => 404], 404);
    }
}
