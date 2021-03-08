<?php

namespace App\Http\Controllers;

use App\Category;
use App\Movement;
use App\Product;
use App\Purchase;
use App\User;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{

    public function show(Request $request,$account_id)
    {
        $return = [];
        $purchases = Purchase::all()
            ->where('user_id', auth()->id())
            ->where('account_id',$account_id)
            ->where('date',request('date'))->flatten();
        foreach ($purchases as $purchase){
            $product = Product::find($purchase['product_id']);
            $formatedProd = [
                'category' => Category::find($product['category_id'])->name,
                'description'=>$product['description'],
                'id'=>$product['id'],
                'image'=>$product['image'],
                'name'=>$product['name'],
                'price_cost'=>$product['price_cost'],
                'original_owner'=>User::find($product['user_id']),
                '_public'=>$product['_public']

            ];
            $return[] = array(
                "id" => $purchase['id'],
                "product" => $formatedProd,
                "quantity" => $purchase['quantity'],
                "total" => $purchase['total']
            );}
        return response()->json(['response' => ['data' => $return,
            'message' => 'Resolving purchase detail for users ID: ' . auth()->id() . ' account: '.$account_id.' date: '.request('date')],
            'error' => null, 'status' => 200], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (request('data') && request('total') && request('account')) {
            $products = request('data');
            foreach ($products as $product) {
                Purchase::create([
                    'user_id' => auth()->id(),
                    'product_id' => $product['product_id'],
                    'account_id' => request('account'),
                    'quantity' => $product['quantity'],
                    'total' => $product['price'] * $product['quantity']
                ]);

                $updatedProduct = Product::find($product['product_id']);

                $inMyList = Product::all()
                    ->where('user_id', auth()->id())
                    ->where('id', $product['product_id'])
                    ->flatten();
                if ($inMyList->count()) {
                    $updatedProduct->inStock += $product['quantity'];
                    $updatedProduct->save();
                } else {
                    Product::create([
                        'user_id' => auth()->id(),
                        'category_id' => $updatedProduct->category_id,
                        'name' => $updatedProduct->name,
                        'image' => $updatedProduct->image,
                        'description' => $updatedProduct->description,
                        'price_cost' => $updatedProduct->price_cost,
                        'inStock' => $product['quantity']
                    ]);
                }

            }

            Movement::create([
                'account_id' => request('account'),
                'user_id' => auth()->id(),
                'concepto_id' => 2,
                'amount' => (request('total') * -1)
            ]);
            return response()->json(['response' => ['data' => null, 'message' => 'Purchase committed !'], 'error' => null, 'status' => 200], 200);

        }
        return response()->json(['response' => null, 'error' => ['message' => 'Fields _data_ _total_ _account_ missing !'], 'status' => 422], 422);
    }

}
