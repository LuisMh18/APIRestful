<?php

namespace App\Http\Controllers\Product;

use App\Product;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class ProductCategoryController extends ApiController
{
  /*  public function __construct()
    {
        $this->middleware('client.credentials')->only(['index']);
        $this->middleware('auth:api')->except(['index']);

        $this->middleware('scope:manage-products')->except('index');
        $this->middleware('can:add-category,product')->only('update');
        $this->middleware('can:delete-category,product')->only('destroy');
    }*/
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Product $product)
    {
        //obtener la lista de categorias de un producto especifico
        $categories = $product->categories;

        return $this->showAll($categories);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product, Category $category)
    {
        // Agregar nuevas categorias existentes a un producto  (update a un producto)
        /*
        * como esta es una relación de muchos a muchos debemos utilizar el metodo syncWithoutDetaching investigar los 3 metodos de abajo
        * metodo -> sync - sustituye la lista anterior de registros en este caso son categorias por la lista de categorias agregadas
        * metodo -> attach - Agrega el nuevo registro(categoria) pero si volvemos a mandar la misma categoria este la vuelve agregar
        * metodo -> syncWithoutDetaching - Agrega la nueva categoria, y si intentamos agregar nuevamente la misma el efecto es nulo(no la agrega), se mantendra la que tendriamos originalmente sin realizar ningun tipo de acctualización
        */
        $product->categories()->syncWithoutDetaching([$category->id]);

        return $this->showAll($product->categories);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, Category $category)
    {
        if (!$product->categories()->find($category->id)) {
            return $this->errorResponse('La categoría especificada no es una categoría de este producto', 404);
        }

        $product->categories()->detach([$category->id]);

        return $this->showAll($product->categories);
    }
}
