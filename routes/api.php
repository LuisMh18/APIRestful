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
Route::resource('buyers.transactions', 'Buyer\BuyerTransactionController', ['only' => ['index']]);
Route::resource('buyers.products', 'Buyer\BuyerProductController', ['only' => ['index']]);
Route::resource('buyers.sellers', 'Buyer\BuyerSellerController', ['only' => ['index']]);
Route::resource('buyers.categories', 'Buyer\BuyerCategoryController', ['only' => ['index']]);

/*
 *categories
*/
Route::resource('categories', 'Category\CategoryController', ['except' => ['create', 'edit']]);
Route::resource('categories.products', 'Category\CategoryProductController',  ['only' => ['index']]);
Route::resource('categories.sellers', 'Category\CategorySellerController',  ['only' => ['index']]);
Route::resource('categories.transactions', 'Category\CategoryTransactionController',  ['only' => ['index']]);

/*
 *products
*/
Route::resource('products', 'Product\ProductController', ['only' => ['index', 'show']]);

/*
 *transactions
*/
Route::resource('transactions', 'Transaction\TransactionController', ['only' => ['index', 'show']]);
Route::resource('transactions.categories', 'Transaction\TransactionCategoryController', ['only' => ['index']]);
Route::resource('transactions.sellers', 'Transaction\TransactionSellerController', ['only' => ['index']]);

/*
 *sellers
*/
Route::resource('sellers', 'Seller\SellerController', ['only' => ['index', 'show']]);

/*
 *users
*/
Route::resource('users', 'User\UserController', ['except' => ['create', 'edit']]);
