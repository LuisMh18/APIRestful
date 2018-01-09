<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        //obtener todos los productos que un camprador ah obtenido(comprado)
        /*Nota: -----
          * - Si obtenemos la lista de todas las transacciones tenemos que pedirle a laravel que para cada una de esas transacciones que vamos a
            - obtener incluya dentro de ellas la lista de los productos, entonces primera accedemos ala relación entre buyer y transactions y esto
            - lo hacemos simplemente poniendo los parentesis() es decir estamos llamando directamente a la función y no a la relación como tal ya no
            - estamos obteniendo una colección si no que estamos obteniendo un query que nos va a permitir agregatr diferentes restricciones a esta
            - consulta, por ejemplo un where un find o cualquier otro tipo de restricción, en este caso para acceder a las relaciones utilizamos en
            - metodo with(), este metodo puede recibir una serie de relaciones, pero en este caso solo vamos a utilizar una que es la de product, vamos
            - a traer el producto de cada una de esas relaciones y lo obtenemos por medio de el metodo get(), de este modo vamos a tener una lista de
            - transacciones cada una de ellas en su interior con un producto, ahora como lo que quereos son solo los productos, para ello existe el metodo
            - llamado pluck() el cual nos permite trabajar directamente con la collecion, eh indicar que quereos obtener solo una parte de esa coleccion completa
            - en este caso solo nos interesa la parte que corresponde a product
        */
        $products = $buyer->transactions()->with('product')
                    ->get()
                    ->pluck('product');
          //dd($products);
          return $this->showAll($products);
    }

}
