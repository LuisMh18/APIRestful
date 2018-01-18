<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategoryTransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        /* obtener la lista de transacciones que se an efectuado para una categoria especifica(solo si existe la transacción)
         * EN este caso no tenemos a certeza de que exista una transacción para ese producto, esto es porq es posible que un producto
         * especifico no se haya vendido aun y por ende no tenga transacciones
         * como existe la posibilidad de que alguna de esas transacciones sea vacia basicamnete porq ese producto aun no tiene ninguna transacción asociada
         * para eso antes de el metodo with vaos a decir que solo queremos los productos que realmente tengan una transacción, es decir por lomenos una transaccion
         * y esto lo hacemos poe medio del metodo whereHas() co este metodo entonces estamos seguros de que los productos que estamos obteniendo son
         * exclusivamente aquellos que ya tienen asociada una transacción
        */
        $transactions = $category->products()
        ->whereHas('transactions')
        ->with('transactions')
        ->get()
        ->pluck('transactions')
        ->collapse();

        return $this->showAll($transactions);
    }

  
}
