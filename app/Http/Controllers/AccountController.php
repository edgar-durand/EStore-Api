<?php

namespace App\Http\Controllers;

use App\Account;
use App\Concepto;
use App\Movement;
use App\Purchase;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $myAccounts = [];

        $accounts = Account::all()
            ->where('user_id', auth()->id())
            ->where('active', true);
        if ($accounts) {
            foreach ($accounts as $account) {

                $total = 0;
                $movements = Movement::all()
                    ->where('account_id', $account->id);

                foreach ($movements as $movement) {
//                    if ($movement->concepto_id === 1 || $movement->concepto_id === 3)
                        $total += $movement->amount;
//                    if ($movement->concepto_id === 2) {
//                        $purchases = Purchase::all()
//                            ->where('account_id', $movement['account_id'])
//                            ->where('movement_id', $movement['id']);
//
//                        foreach ($purchases as $purchase)
//                            if ($purchase->confirmed === 1 || $purchase->confirmed === null)
//                                $total += $purchase->total * -1;
//                    }

                }

                $myAccounts[] = array(
                    'id' => $account->id,
                    'name' => $account->name,
                    'description' => $account->description,
                    'created_at' => $account->created_at,
                    'cash' => $total
                );
            }
            return response()->json(['response' => ['data' => ['accounts' => $myAccounts], 'message' => 'Retrieving user accounts'], 'error' => null, 'status' => 200], 200);
        }
        return response()->json(['response' => ['data' => null, 'message' => 'You have no accounts.'], 'error' => null, 'status' => 200], 200);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (request('name') && request('amount')) {
            $account = Account::all()
                ->where('user_id', auth()->id())
                ->where('name', request('name'));

            if ($account->count()) {
                return response()->json(['response' => null, 'error' => ['message' => 'Account exists !'], 'status' => 422], 422);
            } else {
                Account::create([
                    'name' => request('name'),
                    'description' => request('description'),
                    'user_id' => auth()->id()
                ]);
                $accountId = Account::whereName($request->name)
                    ->where('user_id', auth()->id())->first();

                Movement::create([
                    'account_id' => $accountId->id,
                    'user_id' => auth()->id(),
                    'concepto_id' => 1,
                    'amount' => request('amount')
                ]);
                return response()->json(['response' => ['data' => null, 'message' => 'Created account: ' . $accountId->name . ' !'], 'error' => null, 'status' => 201], 201);
            }
        }
        return response()->json(['response' => null, 'error' => ['message' => '_name_ and _amount_ Required !!'], 'status' => 422], 422);

    }

    /**
     * Display the specified resource.
     *
     * @param \App\Account $account
     * @return \Illuminate\Http\Response
     */
    public function show($account)
    {
        $movements = Movement::all()
            ->where('user_id', auth()->id())
            ->where('account_id', $account);

        $record = [];
        foreach ($movements as $movement) {
            $concept = Concepto::find($movement->concepto_id);
            switch ($concept->id) {
                case 1 :
                    {
                        $record[] = array(
                            'movement_id' => $movement->id,
                            'concept' => $concept->name,
                            'amount' => $movement->amount,
                            'date' => $movement->created_at
                        );
                    }
                    break;
                case 2:
                    {

                        $purchases = Purchase::all()
                            ->where('account_id', $movement['account_id'])
                            ->where('movement_id', $movement['id']);
                        $amount = 0;
                        $confirmed = 0;
                        $declined = 0;
                        $pending = 0;
                        foreach ($purchases as $purchase) {

                            if ($purchase->confirmed === 1) {
                                $confirmed++;
                                $amount += $purchase->total;
                            }
                            if ($purchase->confirmed === 0) {
                                $declined++;
                            }
                            if ($purchase->confirmed === null) {
                                $pending++;
                                $amount += $purchase->total;
                            }
                        }
                        $record[] = array(
                            'movement_id' => $movement->id,
                            'concept' => $concept->name,
                            'amount' => $amount * -1,
                            'confirmed' => $confirmed,
                            'declined' => $declined,
                            'pending' => $pending,
                            'date' => $movement->created_at
                        );
                    }
                    break;
                case 3 :
                    {
                        $record[] = array(
                            'concept' => $concept->name,
                            'movement_id' => $movement->id,
                            'amount' => $movement->amount,
                            'date' => $movement->created_at
                        );
                    }
                    break;

            }


        }

        return $movements->count() ?
            response()->json(['response' => ['data' => $record, 'message' => 'Showing movements for account ID: ' . $account], 'error' => null, 'status' => 200], 200) :
            response()->json(['response' => null, 'error' => ['message' => 'You do not have an account ID: ' . $account . '.'], 'status' => 404], 404);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Account $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $account)
    {
        $toUpdate = Account::find($account)->first();
        if ($toUpdate && $toUpdate->user_id == auth()->id()) {
            if ($request->method() == "PUT") {
                if (request('name') && request('description')) {
                    $toUpdate->name = request('name');
                    $toUpdate->description = request('description');
                    $toUpdate->updated_at = now();
                    $toUpdate->save();
                    return response()->json(['response' => ['data' => null, 'message' => 'Updated !'], 'error' => null, 'status' => 201], 201);
                }
                return response()->json(['response' => null, 'error' => ['message' => '_name_ and _description_ required !'], 'status' => 422], 422);
            }
            if ($request->method() == "PATCH") {
                $columns = [];
                $flag = false;

                if (request('description')) {
                    $flag = true;
                    $columns[] = array('description');
                    $toUpdate->description = request('description');
                }
                if (request('name')) {
                    $flag = true;
                    $columns[] = array('name');
                    $toUpdate->name = request('name');
                }
                if ($flag) {
                    $toUpdate->updated_at = now();
                    $toUpdate->save();
                    return response()->json(['response' => ['data' => $columns, 'message' => 'Updated !'], 'error' => null, 'status' => 201], 201);
                }

                return response()->json(['response' => ['data' => null, 'message' => 'No data to be updated'], 'error' => null, 'status' => 200], 200);
            }
        }
        return response()->json(['response' => null, 'error' => ['message' => 'Account ID: ' . $account . ' Not Found.'], 'status' => 404], 404);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Account $account
     * @return \Illuminate\Http\Response
     */
    public function destroy($account)
    {
        $toDestroy = Account::find($account);
        if ($toDestroy) {
            if ($toDestroy->user_id == auth()->id()) {
                if ($toDestroy->active) {
                    $movements = Movement::all()->where('account_id', $toDestroy->id);
                    $total = 0;
                    foreach ($movements as $movement) {
                        $total += $movement->amount;
                    }
                    if ($total > 0) {
                        return response()->json(['response' => null, 'error' => ['message' => "Account is not empty. You need to transfer $total to another account first."], 'status' => 422], 422);
                    } else {
                        $toDestroy->active = false;
                        $toDestroy->updated_at = now();
                        $toDestroy->save();
                        return response()->json(['response' => ['data' => null, 'message' => 'Account canceled.'], 'error' => null, 'status' => 200], 200);

                    }
                }
                return response()->json(['response' => null, 'error' => ['message' => 'Account ID: ' . $account . ' is not active.'], 'status' => 422], 422);
            }
            return response()->json(['response' => null, 'error' => ['message' => 'Account ID: ' . $account . ' does not belongs to you.'], 'status' => 401], 401);
        }
        return response()->json(['response' => null, 'error' => ['message' => 'Account ID: ' . $account . ' Not Found.'], 'status' => 404], 404);

    }
}
