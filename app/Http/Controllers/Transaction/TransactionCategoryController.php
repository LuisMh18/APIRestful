<?php

namespace App\Http\Controllers\Transaction;

use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class TransactionCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Transaction $transaction)
    {
        //obtener la lista de las categorias respectivas a una transacción especifica
        /*no hay una relación directa entre transaction y category, pero sabemos que una
          transaccion tiene un producto y que ese producto tiene una lista de categorias*/
          /*con esto en base a las relaciones de los modelos, obteniendo el producto de esa transaccioón obtenemos las categorias de ese producto  */
          $categories = $transaction->product->categories;

          return $this->showAll($categories);
    }

}
