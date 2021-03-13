<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>E-Store API</title>
    <link href="dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="dist/css/style.css" rel="stylesheet">
</head>
<body>
@yield('header')
<div class="col-12 container">

    <div class="ibox">
        <div class="ibox-content">

            <table class="table table-bordered table-hover table-light col-12">
                <thead>
                <tr>
                    <th colspan="4" class="text-center alert-dark text-uppercase">Login resources</th>
                </tr>
                <tr>
                    <th>URI</th>
                    <th>METHOD</th>
                    <th>DESCRIPTION</th>
                    <th>TOKEN</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td> api/login</td>
                    <td>POST</td>
                    <td>Login Token returns:
                        <code>
                            {
                            "response":{
                            "data": {
                            "token": [api_token]
                            }
                            }
                            }
                        </code>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>api/logout</td>
                    <td>POST</td>
                    <td>Logout</td>
                    <td>[X]</td>

                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-content">

            <table class="table table-bordered table-hover col-12">
                <thead>
                <tr>
                    <th colspan="4" class="text-center alert-dark text-uppercase">User resources</th>
                </tr>
                <tr>
                    <th>URI</th>
                    <th>METHOD</th>
                    <th>DESCRIPTION</th>
                    <th>TOKEN</th>
                </tr>
                </thead>

                <tbody>
                <tr>
                    <td><a href="{{url('/api/user')}}">api/user</a></td>
                    <td>GET|HEAD</td>
                    <td>Returns all user's emails</td>
                    <td></td>

                </tr>
                <tr>
                    <td>api/user/get_by_id/{user_id}</td>
                    <td>GET|HEAD</td>
                    <td>Returns requested user</td>
                    <td>[X]</td>

                </tr>
                <tr>
                    <td>api/user/{per_page}</td>
                    <td>GET|HEAD</td>
                    <td>Returns users by paginated</td>
                    <td>[X]</td>

                </tr>
                <tr>
                    <td colspan="4" class="text-justify">
                        <section>
                            @if($users->count())
                                <code>{<br/>
                                    "response": {<br/>
                                    "current_page": 1,<br/>
                                    "data":[<br/></code>
                                <small>

                                    @foreach($users as $user)
                                        {{($user)}}<br/>
                                    @endforeach
                                </small>
                                <code>],<br/>
                                    "first_page_url": "http://{SERVER_ADDR}:{SERVER_PORT}/api/user?page=1",<br/>
                                    "from": 1,<br/>
                                    "last_page": 275,<br/>
                                    "last_page_url": "http://{SERVER_ADDR}:{SERVER_PORT}/api/user?page=275",<br/>
                                    "next_page_url": "http://{SERVER_ADDR}:{SERVER_PORT}/api/user?page=2",<br/>
                                    "path": "http://{SERVER_ADDR}:{SERVER_PORT}/api/user",<br/>
                                    "per_page": 20,<br/>
                                    "prev_page_url": null,<br/>
                                    "to": 20,<br/>
                                    "total": 5496<br/>
                                    },<br/>
                                    "error": null,<br/>
                                    "status": 200<br/>}</code>
                            @else
                                <h1 class="text-danger">DB connexion Error !</h1>
                            @endif

                        </section>
                    </td>
                </tr>
                <tr>
                    <td>api/user</td>
                    <td>POST</td>
                    <td>Create new user</td>
                    <td>[X]</td>

                </tr>
                <tr>
                    <td>api/profile</td>
                    <td>GET|HEAD</td>
                    <td>Show current user profile</td>
                    <td>[X]</td>

                </tr>
                <tr>
                    <td>api/user/{user_id}</td>
                    <td>PUT|PATCH</td>
                    <td>Updates profile</td>
                    <td>[X]</td>
                </tr>


                </tbody>
            </table>


        </div>
    </div>

    <div class="ibox">
        <div class="ibox-content">

            <table class="table table-bordered table-hover table-light col-12">
                <thead>
                <tr>
                    <th colspan="4" class="text-center alert-dark text-uppercase">Category resources</th>
                </tr>
                <tr>
                    <th>URI</th>
                    <th>METHOD</th>
                    <th>DESCRIPTION</th>
                    <th>TOKEN</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>api/category</td>
                    <td>POST</td>
                    <td>Store new category</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/category</td>
                    <td>GET|HEAD</td>
                    <td>Returns all categories</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/category/{category_id}</td>
                    <td>PUT|PATCH</td>
                    <td>Updates a category</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/category/{category_id}</td>
                    <td>DELETE</td>
                    <td>Destroy a category</td>
                    <td>[X]</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-content">

            <table class="table table-bordered table-hover table-light col-12">
                <thead>
                <tr>
                    <th colspan="4" class="text-center alert-dark text-uppercase">Product resources</th>
                </tr>
                <tr>
                    <th>URI</th>
                    <th>METHOD</th>
                    <th>DESCRIPTION</th>
                    <th>TOKEN</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>api/product</td>
                    <td>POST</td>
                    <td>Store new product</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td><a href="/api/product/20">api/product/{per_page}</a></td>
                    <td>GET|HEAD</td>
                    <td>Returns all public products</td>
                    <td></td>
                </tr>
                <tr>
                    <td>api/my_product</td>
                    <td>GET|HEAD</td>
                    <td>Returns all your products</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/product/{product_id}</td>
                    <td>PUT|PATCH</td>
                    <td>Updates a product</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/product_detail/{product_id}</td>
                    <td>GET|HEAD</td>
                    <td>Show a product</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/product/{product_id}</td>
                    <td>DELETE</td>
                    <td>Destroy a product</td>
                    <td>[X]</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-content">

            <table class="table table-bordered table-hover table-light col-12">
                <thead>
                <tr>
                    <th colspan="4" class="text-center alert-dark text-uppercase">Concept resources</th>
                </tr>
                <tr>
                    <th>URI</th>
                    <th>METHOD</th>
                    <th>DESCRIPTION</th>
                    <th>TOKEN</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>api/concept</td>
                    <td>POST</td>
                    <td>Store new concept</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/concept</td>
                    <td>GET|HEAD</td>
                    <td>Returns all concepts</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/concept/{concept_id}</td>
                    <td>PUT|PATCH</td>
                    <td>Updates a concept</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/concept/{concept_id}</td>
                    <td>DELETE</td>
                    <td>Destroy a concept</td>
                    <td>[X]</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-content">

            <table class="table table-bordered table-hover table-light col-12">
                <thead>
                <tr>
                    <th colspan="4" class="text-center alert-dark text-uppercase">Account resources</th>
                </tr>
                <tr>
                    <th>URI</th>
                    <th>METHOD</th>
                    <th>DESCRIPTION</th>
                    <th>TOKEN</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>api/account</td>
                    <td>POST</td>
                    <td>Store new account</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/account</td>
                    <td>GET|HEAD</td>
                    <td>Returns all user's accounts</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/account/{account_id}</td>
                    <td>PUT|PATCH</td>
                    <td>Updates an account</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/account/{account_id}</td>
                    <td>GET|HEAD</td>
                    <td>Returns all account's movements</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/account/{account_id}</td>
                    <td>DELETE</td>
                    <td>Cancel an account</td>
                    <td>[X]</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="ibox">
        <div class="ibox-content">

            <table class="table table-bordered table-hover table-light col-12">
                <thead>
                <tr>
                    <th colspan="4" class="text-center alert-dark text-uppercase">Purchase resources</th>
                </tr>
                <tr>
                    <th>URI</th>
                    <th>METHOD</th>
                    <th>DESCRIPTION</th>
                    <th>TOKEN</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>api/purchase</td>
                    <td>POST</td>
                    <td>Commit a purchase</td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td class="align-middle">api/purchase/confirm</td>
                    <td class="align-middle">POST</td>
                    <td>Confirm a purchase. [Send body]:</td>
                    <td class="align-middle">[X]</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <code><br/>{<br/>
                            "data":[<br/>
                            {"id": [purchase_id], "product_id": [product_id],<br/>
                            "user_id": [user_id], "account_id": [account_id],<br/>
                            "movement_id": [movement_id], "quantity": [quantity], "total": [total]}, <br/>
                            {[-other-purchase-]},...<br/>
                            ],<br/><br/>}</code>
                    </td>
                </tr>
                <tr>
                    <td class="align-middle">api/purchase/decline</td>
                    <td class="align-middle">POST</td>
                    <td>Decline a purchase. [Send body]:</td>
                    <td class="align-middle">[X]</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <code><br/>{<br/>
                            "data":[<br/>
                            {"id": [purchase_id], "product_id": [product_id],<br/>
                            "user_id": [user_id], "account_id": [account_id],<br/>
                            "movement_id": [movement_id], "quantity": [quantity], "total": [total]}, <br/>
                            {[-other-decline-]},...<br/>
                            ],<br/><br/>}</code>

                    </td>
                </tr>
                <tr>
                    <td class="align-middle">api/purchase/get_pending</td>
                    <td class="align-middle">GET|HEAD</td>
                    <td>Get pending purchases.</td>
                    <td class="align-middle">[X]</td>
                </tr>
                <tr>
                    <td class="align-middle">api/purchase/get_declined</td>
                    <td class="align-middle">GET|HEAD</td>
                    <td>Get declined purchases.</td>
                    <td class="align-middle">[X]</td>
                </tr>
                <tr>
                    <td class="align-middle">api/purchase/get_confirmed</td>
                    <td class="align-middle">GET|HEAD</td>
                    <td>Get confirmed purchases.</td>
                    <td class="align-middle">[X]</td>
                </tr>
                <tr>
                    <td class="align-middle">api/purchase/get_all</td>
                    <td class="align-middle">GET|HEAD</td>
                    <td>Get all purchases.</td>
                    <td class="align-middle">[X]</td>
                </tr>
                <tr>
                    <td>api/purchase/{account_id}</td>
                    <td>POST</td>
                    <td>Purchase detail. Body send: <code>{"movement_id":[movement_id]}</code></td>
                    <td>[X]</td>
                </tr>
                <tr>
                    <td>api/purchase/sale_request</td>
                    <td>GET|HEAD</td>
                    <td>Returns sales request that comes from another user to you.</td>
                    <td>[X]</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
<div class="social-footer">
    <div class="ibox-title">
        Ex sending Token in Header: <code>Authorization: Bearer [api_token] </code>
    </div>

</div>
</body>
</html>
