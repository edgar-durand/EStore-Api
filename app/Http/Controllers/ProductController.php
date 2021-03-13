<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $perPage)
    {
        if ($search = trim($request->get('search'))) {

            $paginated = Product::from('products as p')
                ->where(function ($query) use ($search){
                    $query = $query->orWhere('p.name','like', "%$search%");
                    $query = $query->orWhere('p.description','like', "%$search%");
                    $query = $query->orWhere('p.price_cost','like', "%$search%");
                    $query = $query->orWhere('p.sales_price','like', "%$search%");
                })
                ->where('_public', true);
            $paginated = $paginated
                ->orderBy('name', 'asc')
                ->paginate($perPage);
//                ->forPage($page, $perPage);
            return $paginated ?
                response()->json(['response' =>  $paginated, 'error' => null, 'status' => 200], 200) :
                response()->json(['response' => null, 'error' => ['message' => 'No Found !'], 'status' => 404], 404);

        }
        $product = Product::from('products')
            ->where('_public', true)
            ->orderBy('name', 'asc')
            ->paginate($perPage);
        return $product->count() ?
            response()->json(['response' => $product, 'status' => 200, 'error' => null], 200) :
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
        $newProduct = null;
        if ($request->name && $request->category_id && $request->price_cost) {
            $name = Product::whereName($request->name)->where('sales_price', $request->price_cost)->first();
            if ($name) {
                return response()->json(['response' => null, 'error' => ['message' => 'This Product name and Sales Price already exist !'], 'status' => 422], 422);
            } else {
                $newProduct = Product::create([
                    'user_id' => auth()->id(),
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                    'image' => $request->image,
                    'description' => $request->description,
                    'price_cost' => $request->price_cost,
                    'sales_price' => $request->sales_price,
                    'inStock' => $request->inStock,
                    '_public' => $request->_public
                ]);
                $newProduct->save();
                $category = Category::find($request->category_id)->first();
                return response()->json(['response' => ['data' => ['name' => $newProduct->name,
                    'description' => $newProduct->description,
                    'id' => $newProduct->id,
                    'image' => $newProduct->image,
                    'inStock' => $newProduct->inStock,
                    'price_cost' => $newProduct->price_cost,
                    'sales_price' => $newProduct->sales_price,
                    '_public' => $newProduct->_public,
                    'category' => $category->name], 'message' => 'created !'], 'error' => null, 'status' => 201], 201);
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
    public function show($product)
    {
        $showProduct = Product::find($product);
        $category = Category::find($showProduct->category_id);
        $showing= [
            'name' => $showProduct->name,
            'description' => $showProduct->description,
            'id' => $showProduct->id,
            'image' => $showProduct->image,
            'inStock' => $showProduct->inStock,
            'price_cost' => $showProduct->price_cost,
            'sales_price' => $showProduct->sales_price,
            '_public' => $showProduct->_public,
            'category' => $category->name
        ];
        return $product ?
            response()->json(['response' => ['data' => $showing, 'message' => 'Resolving product for ID: ' . $product . ''], 'error' => null, 'status' => 200], 200) :
            response()->json(['response' => null, 'error' => ['message' => 'ID ' . $product . ' No Found !'], 'status' => 404], 404);
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
        $showing = [];
        $products = Product::all()->where('user_id', auth()->id())->flatten();
        foreach ($products as $product){
            $category = Category::find($product->category_id)->first();
            $showing[] = array(
                'name' => $product->name,
                'description' => $product->description,
                'id' => $product->id,
                'image' => $product->image,
                'inStock' => $product->inStock,
                'price_cost' => $product->price_cost,
                'sales_price' => $product->sales_price,
                '_public' => $product->_public,
                'category' => $category->name
            );
        }

        return $products->count() ?
            response()->json(['response' => ['data' => $showing, 'message' => 'Your products delivered.'], 'error' => null, 'status' => 200], 200) :
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
                    $toUpdate->sales_price = $request->sales_price;
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
                    if ($request->sales_price) {
                        $flag = true;
                        $columns[] = array('sales_price');
                        $toUpdate->sales_price = $request->sales_price;
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
