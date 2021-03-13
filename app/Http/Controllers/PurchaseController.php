<?php

namespace App\Http\Controllers;

use App\Account;
use App\Category;
use App\Movement;
use App\Product;
use App\Purchase;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{

    public function show(Request $request, $account_id)
    {
        $return = [];
        $purchases = Purchase::all()
            ->where('user_id', auth()->id())
            ->where('account_id', $account_id)
            ->where('movement_id', request('movement_id'))->flatten();
        foreach ($purchases as $purchase) {
            $product = Product::find($purchase['product_id']);
            $formatedProd = [
                'category' => Category::find($product['category_id'])->name,
                'description' => $product['description'],
                'id' => $product['id'],
                'image' => $product['image'],
                'name' => $product['name'],
                'price_cost' => $product['price_cost'],
                'original_owner' => User::find($product['user_id']),
                '_public' => $product['_public']

            ];
            $return[] = array(
                "id" => $purchase['id'],
                "product" => $formatedProd,
                "quantity" => $purchase['quantity'],
                "confirmed" => $purchase['confirmed'],
                "total" => $purchase['total']
            );
        }
        return response()->json(['response' => ['data' => $return,
            'message' => 'Resolving purchase detail for users ID: ' . auth()->id() . ' account: ' . $account_id . ' date: ' . request('date')],
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
            $movement_id = Movement::create([
                'account_id' => request('account'),
                'user_id' => auth()->id(),
                'concepto_id' => 2,
                'amount' => (request('total') * -1)
            ]);
            $movement_id->save();


            foreach ($products as $product) {
                $inMyList = Product::whereId($product['product_id'])
                    ->where('user_id', auth()->id())->first();

                if (!is_null($inMyList) && $inMyList->count()) {
                    $inMyList->inStock += $product['quantity'];
                    $inMyList->save();

                    Purchase::create([
                        'user_id' => auth()->id(),
                        'product_id' => $product['product_id'],
                        'movement_id' => $movement_id->id,
                        'account_id' => request('account'),
                        'quantity' => $product['quantity'],
                        'confirmed' => true,
                        'total' => $product['price'] * $product['quantity']
                    ]);

                } else {
                    Purchase::create([
                        'user_id' => auth()->id(),
                        'product_id' => $product['product_id'],
                        'movement_id' => $movement_id->id,
                        'account_id' => request('account'),
                        'quantity' => $product['quantity'],
                        'total' => $product['price'] * $product['quantity']
                    ]);
                }

            }

            return response()->json(['response' => ['data' => null, 'message' => 'Purchase committed !'], 'error' => null, 'status' => 200], 200);

        }
        return response()->json(['response' => null, 'error' => ['message' => 'Fields _data_ _total_ _account_ missing !'], 'status' => 422], 422);
    }

    public function saleRequest()
    {
        $myRequest = Purchase::all()
            ->where('confirmed', '===', null)
            ->where('user_id', '!==', auth()->id())
            ->flatten();

        $Results = null;
        if ($myRequest->count())
            foreach ($myRequest as $purchase) {
                $product = Product::all()
                    ->where('id', $purchase['product_id'])
                    ->where('user_id', auth()->id())->first();


                !is_null($product) && $product->count() &&
                $Results[] = [
                    'user_requesting' => User::find($purchase->user_id),
                    'product' => $product,
                    'quantity' => $purchase['quantity'],
                    'movement_id'=>$purchase['movement_id'],
                    'account_id'=>$purchase['account_id'],
                    'total' => $purchase['total'],
                    'purchase_id' => $purchase['id'],
                    'date' => $purchase['created_at']
                ];

            }
        return !is_null($Results) ?
            response()->json(['response' => ['data' => $Results, 'message' => 'Pending request.'], 'error' => null, 'status' => 200], 200) :
            response()->json(['response' => ['data' => null, 'message' => 'No purchases request.'], 'error' => null, 'status' => 200], 200);
    }

    public function confirm(Request $request)
    {
        if ($request->get('data')) {
            $flag = false;
            foreach ($request->get('data') as $purchase) {
                $current_purchase = Purchase::find($purchase['id']);
//                    ->where('user_id', $purchase['user_id'])
//                    ->where('product_id', $purchase['product_id'])
//                    ->where('movement_id', $purchase['movement_id'])->get()->first();

                if ($current_purchase->count()) {
                    $flag = true;
                    $current_purchase->confirmed = true;
                    $current_purchase->save();


                    $movement = Movement::find($current_purchase->movement_id);
                    $movement->updated_at = now();
                    git$movement->save();

                    $updatedProduct = Product::find($purchase['product_id']);


                    Product::create([
                        'user_id' => auth()->id(),
                        'category_id' => $updatedProduct->category_id,
                        'name' => $updatedProduct->name,
                        'image' => $updatedProduct->image,
                        'description' => $updatedProduct->description,
                        'price_cost' => $updatedProduct->price_cost,
                        'sales_price' => 0,
                        'inStock' => $purchase['quantity']
                    ]);

//                    GENERARLE UNA VENTA AL USUARIO QUE VENDE---------
                    Movement::create([
                       'account_id'=>$purchase['my_account_id'],
                        'concepto_id'=>3,
                        'user_id'=>auth()->id(),
                        'amount'=>$purchase['total']
                    ]);

                }
            }
            if ($flag)
                return response()->json(["response" => ["data" => null, "message" => "Confirmed"], "error" => null, "status" => 200], 200);
            return response()->json(["response" => null, "error" => ["message" => "No data matches in DB.!"], "status" => 422], 422);
        }


    }

    public function decline(Request $request)
    {
        if ($request->get('data')) {
            $flag = false;
            foreach ($request->get('data') as $purchase) {
                $current_purchase = Purchase::find($purchase['id'])
                    ->where('user_id', $purchase['user_id'])
                    ->where('movement_id', $purchase['movement_id'])
                    ->get()->first();

                if ($current_purchase->count()) {
                    $flag = true;
                    $current_purchase->confirmed = false;
                    $current_purchase->save();


                    $movement = Movement::find($current_purchase->movement_id);
                    $movement->amount += $purchase['total'];
                    $movement->updated_at = now();
                    $movement->save();

                }
            }
            if ($flag)
                return response()->json(["response" => ["data" => $request->get('data'), "message" => "Declined"], "error" => null, "status" => 200], 200);
            return response()->json(["response" => null, "error" => ["message" => "No data matches in DB.!"], "status" => 422], 422);
        }


    }

    public function getPendingPurchase()
    {
        $myAccounts = Account::all()
            ->where('user_id', auth()->id())
            ->where('active', true);
        $formatted = null;
        foreach ($myAccounts as $account) {
            $verify = Purchase::all()
                ->where('user_id', auth()->id())
                ->where('account_id', $account->id)
                ->where('confirmed', '===', null)->flatten();

            if ($verify->count()) {
                foreach ($verify as $v)
                    $formatted[] = [
                        "id" => $v->id,
                        "product" => Product::find($v->product_id),
                        "date" => $v->created_at,
                        "account" => Account::find($v->account_id),
                        "confirmed" => $v->confirmed,
                        "quantity" => $v->quantity,
                        "total" => $v->total
                    ];
            }


        }

        return !is_null($formatted) ?
            response()->json(['response' => ['data' => $formatted, 'message' => 'Pending purchases.'], 'error' => null, 'status' => 200], 200) :
            response()->json(['response' => ['data' => null, 'message' => 'No pending purchases.'], 'error' => null, 'status' => 200], 200);
    }

    public function getConfirmedPurchase()
    {
        $myAccounts = Account::all()
            ->where('user_id', auth()->id())
            ->where('active', true);
        $formatted = null;
        foreach ($myAccounts as $account) {
            $verify = Purchase::all()
                ->where('user_id', auth()->id())
                ->where('account_id', $account->id)
                ->where('confirmed', true)->flatten();

            if ($verify->count()) {
                foreach ($verify as $v)
                    $formatted[] = [
                        "id" => $v->id,
                        "product" => Product::find($v->product_id),
                        "date" => $v->created_at,
                        "account" => Account::find($v->account_id),
                        "confirmed" => $v->confirmed,
                        "quantity" => $v->quantity,
                        "total" => $v->total
                    ];
            }


        }

        return !is_null($formatted) ?
            response()->json(['response' => ['data' => $formatted, 'message' => 'Confirmed purchases.'], 'error' => null, 'status' => 200], 200) :
            response()->json(['response' => ['data' => null, 'message' => 'No confirmed purchases.'], 'error' => null, 'status' => 200], 200);
    }

    public function getDeclinedPurchase()
    {

        $formatted = null;
        $myAccounts = Account::all()
            ->where('user_id', auth()->id())
            ->where('active', true);

        foreach ($myAccounts as $account) {
            $verify = Purchase::all()
                ->where('user_id', auth()->id())
                ->where('account_id', $account->id)
                ->where('confirmed', '===', 0)->flatten();

            if ($verify->count()) {
                foreach ($verify as $v)
                    $formatted[] = [
                        "id" => $v->id,
                        "product" => Product::find($v->product_id),
                        "date" => $v->created_at,
                        "account" => Account::find($v->account_id),
                        "confirmed" => $v->confirmed,
                        "quantity" => $v->quantity,
                        "total" => $v->total
                    ];
            }


        }

        return !is_null($formatted) ?
            response()->json(['response' => ['data' => $formatted, 'message' => 'Declined purchases.'], 'error' => null, 'status' => 200], 200) :
            response()->json(['response' => ['data' => null, 'message' => 'No declined purchases.'], 'error' => null, 'status' => 200], 200);
    }

    public function getAllPurchase()
    {

        $formatted = null;
        $myAccounts = Account::all()
            ->where('user_id', auth()->id())
            ->where('active', true);

        foreach ($myAccounts as $account) {
            $verify = Purchase::all()
                ->where('user_id', auth()->id())
                ->where('account_id', $account->id)->flatten();

            if ($verify->count()) {
                foreach ($verify as $v) {
                    $formatted[] = [
                        "id" => $v->id,
                        "product" => Product::find($v->product_id),
                        "date" => $v->created_at,
                        "account" => Account::find($v->account_id),
                        "confirmed" => $v->confirmed,
                        "quantity" => $v->quantity,
                        "total" => $v->total
                    ];
                }
            }


        }

        return !is_null($formatted) ?
            response()->json(['response' => ['data' => $formatted, 'message' => 'All purchases.'], 'error' => null, 'status' => 200], 200) :
            response()->json(['response' => ['data' => null, 'message' => 'No purchases.'], 'error' => null, 'status' => 200], 200);
    }

}
