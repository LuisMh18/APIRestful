<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategoryBuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        /* obtener la lista de los compradores de una categoria especifica
         * Esta operacion va requerir la union de todo lo que emos visto
         * la unica restriccion aqui es q al tener la lista de estos compradores es posible que estos se rpitan oh que no existan
         * tendremos que ir a la lista de productos de esa categoria y luego para cada uno de esos productos cargar las lista de transacciones
         * y esas transacciones cargarlas junto con el comprador de la misma
         * 
        */
        $buyers = $category->products()
        ->whereHas('transactions') //nos aseguramos de que los productos tengan transacciones
        ->with('transactions.buyer') //solicitamos dentro de cada una de esas transacciones el comprador, como es una relación compuesta obtenemos primero las transacciones junto con el comprador de tales transacciones
        ->get()
        ->pluck('transactions') //una vez que tenemos los resultados como solo nos interesan las transacciones las obtenemos por medio del metodo pluck()
        ->collapse() //juntamos todas las colecciiones de las transacciones, esta es la razon por la cual en el metodo pluck de arriba no podemos decir transactions.buyers porque no estariaos accediendo de la manera correcta, basicamente al tenerdiferentes colecciones y no una sola laravel no seria capas de acceder por medio de la notacion de puntos a los elementos de entre cada una
        ->pluck('buyer')//una vez que tenemos una unica coleccion utilizamos pluck exta vez para obtener los compradores
        ->unique()
        ->values();
    
        return $this->showAll($buyers);
    }

   
}
