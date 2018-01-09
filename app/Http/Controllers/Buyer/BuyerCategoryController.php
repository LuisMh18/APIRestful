<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
      //obtener la lista de categorias en las cuales un comprador a realizado compras
      /*Nota: -----
        * - En este caso estamos obteniendo una serie de colecciones, esto sucede porq basicamente laravel esta construllendo la lisa de
          - catwgorias de un producto yla junta con otra lista de categorias entonces lo q tenemos es una coleccion con otra serie de
          - colecciones al interior, sin embargo lo que necesitamos unicamente es obtener una lista y no una lista con listas, ahora para
          - juntar todas las listas en una sola, laravel nos proporciona un metodo llamado collapse() que basicamente tomara toda esa serie
          - de listas y las juntara en una sola lista.
      */
      $categories = $buyer->transactions()->with('product.categories')
                  ->get()
                  ->pluck('product.categories')
                  ->collapse()
                  ->unique('id')
                  ->values();
        //dd($categories);
        return $this->showAll($categories);
    }


}
