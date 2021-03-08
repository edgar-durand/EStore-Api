<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{


    public function index()
    {
//         EXAMPLES:
//        User::chunk(200, function ($flights) {
//            foreach ($users as $user) {
//                //
//            }
//        });


//        $users = App\User::cursor()->filter(function ($user) {
//            return $user->id > 500;
//        });

//        $flights = App\Flight::find([1, 2, 3]);

//        $model = App\Flight::where('legs', '>', 100)->firstOr(function () {
//            // ...
//        });

//        App\Flight::where('active', 1)
//            ->where('destination', 'San Diego')
//            ->update(['delayed' => 1]);

//        $user->wasChanged('first_name'); // false

        // Retrieve flight by name, or create it with the name, delayed, and arrival_time attributes...
//        $flight = App\Flight::firstOrCreate(
//            ['name' => 'Flight 10'],
//            ['delayed' => 1, 'arrival_time' => '11:30']
//        );

        // If there's a flight from Oakland to San Diego, set the price to $99.
// If no matching model exists, create one.
//        $flight = App\Flight::updateOrCreate(
//            ['departure' => 'Oakland', 'destination' => 'San Diego'],
//            ['price' => 99, 'discounted' => 1]
//        );


        $users = User::all(['email']);
        return $users ?
            response()->json(['response' => ['data' => $users, 'message' => 'Resolving all users.'], 'error' => null, 'status' => 200], 200) :
            response()->json(['response' => null, 'error' => ['message' => 'No Found !'], 'status' => 404], 404);
    }

    public function getByPage($page,$perPage)
    {

        $paginated = User::all()->forPage($page,$perPage);
        $allResults = User::all()->count();
        $allPages = ceil($allResults / $perPage) ;
        return $paginated ?
            response()->json(['response' => ['data' => ['results'=>$paginated,'all_results'=>$allResults,'all_pages'=>$allPages], 'message' => 'Resolving paginated users.'], 'error' => null, 'status' => 200], 200) :
            response()->json(['response' => null, 'error' => ['message' => 'No Found !'], 'status' => 404], 404);
    }


    public function store(Request $request)
    {
        if ($request->email && $request->password) {
            $email = User::whereEmail($request->email)->first();
            if (!$email) {
                User::create([
                    'username' => $request->username,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'birth_date' => $request->birth_date,
                    'photo' => $request->photo,

                    'street' => $request->street,
                    'building' => $request->building,
                    'number' => $request->number,
                    'between' => $request->between,
                    'municipality' => $request->municipality,
                    'province' => $request->province,
                    'phone' => $request->phone,

                    'facebook' => $request->facebook,
                    'twitter' => $request->twitter,
                    'instagram' => $request->instagram,

                    'status_message' => $request->status_message,


                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);
                return response()->json(['response' => ['data' => null, 'message' => 'created !'], 'error' => null, 'status' => 201], 201);
            } else return response()->json(['response' => null, 'error' => ['message' => 'Email is in use !'], 'status' => 422], 422);
        }
        return response()->json(['response' => null, 'error' => ['message' => 'Data is invalid or null !'], 'status' => 422], 422);
    }


    public function show()
    {
        return response()->json(['response' => ['data' => User::find(auth()->id()), 'message' => 'Resolving profile for users ID: ' . auth()->id() . ''], 'error' => null, 'status' => 200], 200);
    }


    public function logout()
    {
        $user = auth()->user();
        $user->api_token = null;
        $user->save();
        return response()->json(['response' => ['data' => null, 'message' => $user->name . ' logged out !'], 'error' => null, 'status' => 200], 200);
    }


    public function login(Request $request)
    {

        $user = User::whereEmail($request->email)->first();
        if (!is_null($user) && Hash::check($request->password, $user->password)) {
            if (!is_null($user->api_token)) {
                return response()->json(['response' => ['data' => ['token' => $user->api_token], 'message' => 'You were authenticated !'], 'error' => null, 'status' => 200], 200);
            } else {
                $user->api_token = Str::random(50);
                $user->save();
                return response()->json(['response' => ['data' => ['token' => $user->api_token], 'message' => 'Now you are authenticated !'], 'error' => null, 'status' => 200], 200);
            }
        } else
            return response()->json(['response' => null, 'error' => ['message' => 'Incorrect !'], 'status' => 422], 422);
    }


    public
    function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($user) {
            if ($request->method() === 'PUT') {
                if ($request->email && $request->password) {

                    $user->username = $request->username;
                    $user->email = $request->email;
                    $user->first_name = $request->first_name;
                    $user->last_name = $request->last_name;
                    $user->birth_date = $request->birth_date;
                    $user->photo = $request->photo;

                    $user->is_root = $request->is_root;
                    $user->is_active = $request->is_active;

                    $user->street = $request->street;
                    $user->building = $request->building;
                    $user->number = $request->number;
                    $user->between = $request->between;
                    $user->municipality = $request->municipality;
                    $user->province = $request->province;
                    $user->phone = $request->phone;

                    $user->facebook = $request->facebook;
                    $user->twitter = $request->twitter;
                    $user->instagram = $request->instagram;

                    $user->status_message = $request->status_message;

                    $user->password = Hash::make($request->password);
                    $user->save();
                    return response()->json(['response' => ['data' => null, 'message' => 'Updated !'], 'error' => null, 'status' => 202], 202);
                }
                return response()->json(['response' => null, 'error' => ['message' => 'Data is invalid or null !'], 'status' => 422], 422);
            }
            if ($request->method() === 'PATCH') {
                $flag = false;
                $columns = [];

                if ($request->username) {
                    $flag = true;
                    $columns[] = array('username');
                    $user->username = $request->username;
                }
                if ($request->get('email')) {
                    $flag = true;
                    $columns[] = array('email');
                    $user->email = $request->get('email');
                }
                if ($request->get('password')) {
                    $flag = true;
                    $columns[] = array('password');
                    $user->password = Hash::make($request->get('password'));
                }
                if ($request->first_name) {
                    $flag = true;
                    $columns[] = array('first_name');
                    $user->first_name = $request->first_name;
                }
                if ($request->last_name) {
                    $flag = true;
                    $columns[] = array('last_name');
                    $user->last_name = $request->last_name;
                }
                if ($request->birth_date) {
                    $flag = true;
                    $columns[] = array('birth_date');
                    $user->birth_date = $request->birth_date;
                }
                if ($request->photo) {
                    $flag = true;
                    $columns[] = array('photo');
                    $user->photo = $request->photo;
                }
                if ($request->is_root) {
                    $flag = true;
                    $columns[] = array('is_root');
                    $user->is_root = $request->is_root;
                }
                if ($request->is_active) {
                    $flag = true;
                    $columns[] = array('is_active');
                    $user->is_active = $request->is_active;
                }
                if ($request->street) {
                    $flag = true;
                    $columns[] = array('street');
                    $user->street = $request->street;
                }
                if ($request->building) {
                    $flag = true;
                    $columns[] = array('building');
                    $user->building = $request->building;
                }
                if ($request->number) {
                    $flag = true;
                    $columns[] = array('number');
                    $user->number = $request->number;
                }
                if ($request->between) {
                    $flag = true;
                    $columns[] = array('between');
                    $user->between = $request->between;
                }
                if ($request->municipality) {
                    $flag = true;
                    $columns[] = array('municipality');
                    $user->municipality = $request->municipality;
                }
                if ($request->province) {
                    $flag = true;
                    $columns[] = array('province');
                    $user->province = $request->province;
                }
                if ($request->phone) {
                    $flag = true;
                    $columns[] = array('phone');
                    $user->phone = $request->phone;
                }
                if ($request->facebook) {
                    $flag = true;
                    $columns[] = array('facebook');
                    $user->facebook = $request->facebook;
                }
                if ($request->twitter) {
                    $flag = true;
                    $columns[] = array('twitter');
                    $user->twitter = $request->twitter;
                }
                if ($request->instagram) {
                    $flag = true;
                    $columns[] = array('instagram');
                    $user->instagram = $request->instagram;
                }
                if ($request->status_message) {
                    $flag = true;
                    $columns[] = array('status_message');
                    $user->status_message = $request->status_message;
                }
                if ($flag) {
                    $user->save();
                    return response()->json(['response' => ['data' => $columns, 'message' => 'Updated !'], 'error' => null, 'status' => 202], 202);
                }
                return response()->json(['response' => ['data' => null, 'message' => 'No data provided, Nothing has changed !'], 'error' => null, 'status' => 200], 200);
            }
        }
        return response()->json(['response' => null, 'error' => ['message' => 'User ID ' . $id . ' No Found !'], 'status' => 404], 404);

    }


//Only ForRoots
    public
    function destroy($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json(['response' => ['data' => null, 'message' => 'User is been deleted !'], 'error' => null, 'status' => 200], 200);
        }
        return response()->json(['response' => null, 'error' => ['message' => 'User ID ' . $id . ' No Found !'], 'status' => 404], 404);
    }
}
