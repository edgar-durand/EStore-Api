<?php

namespace App\Http\Controllers;

use App\Movement;
use App\Product;
use App\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{

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
                    'quantity' => $product['quantity'],
                    'total' => $product['price'] * $product['quantity']
                ]);

                $updatedProduct = Product::find($product['product_id']);
                $updatedProduct->inStock += $product['quantity'];
                $updatedProduct->save();
            }

            Movement::create([
                'account_id' => request('account'),
                'user_id' => auth()->id(),
                'concepto_id' => 2,
                'amount' => (request('total') * -1)
            ]);
            return response()->json(['response' => ['data'=>null,'message'=>'Purchase committed !'], 'error' => null, 'status' => 200], 200);

        }
        return response()->json(['response' => null, 'error' => ['message' => 'Fields _data_ _total_ _account_ missing !'], 'status' => 422], 422);
    }

}
