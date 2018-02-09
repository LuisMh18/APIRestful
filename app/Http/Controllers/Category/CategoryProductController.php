<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategoryProductController extends ApiController
{

    public function __construct()
    {
       /*middleware client.credentials para proporcionar acceso a un cliente valido del sistema sin necesidad de que envie o sea autorizado por un usuario, de este
       modo cualquier cliente registrado en el sistema va poder acceder a las rutas especificadas en el middleware de passport client.credentials, y por supuesto al no tener autorización 
       o relación con un usuario especifico no podriamos tener un control si por medio de ese token se puede realizar esa acción o no, lo que quiere decir entonces es que
       vamos a proteger las rutas mas basicas de nuestro sistema, en este caso entonces cualquier usuario, cliente registrado en el sistema deberia poder ver la lista de 
       productos, la lista de categorias, registrar un nuevo usuario, solicitar reenviar un correo electronico y similar.*/
       $this->middleware('client.credentials')->only(['index']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Category $category)
    {
        //obtener la lista de todos los productos asociados a una categoria dada
        $products = $category->products;

          return $this->showAll($products);
    }


}
