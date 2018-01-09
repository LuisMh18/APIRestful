<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes

|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/


/*
 *buyers
*/
Route::resource('buyers', 'Buyer\BuyerController', ['only' => ['index', 'show']]);

/*
 *categories
*/
Route::resource('categories', 'Category\CategoryController', ['except' => ['create', 'edit']]);

/*
 *products
*/
Route::resource('products', 'Product\ProductController', ['only' => ['index', 'show']]);

/*
 *transactions
*/
Route::resource('transactions', 'Transaction\TransactionController', ['only' => ['index', 'show']]);
Route::resource('transactions.categories', 'Transaction\TransactionCategoryController', ['only' => ['index']]);

/*
 *sellers
*/
Route::resource('sellers', 'Seller\SellerController', ['only' => ['index', 'show']]);

/*
 *users
*/
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);
