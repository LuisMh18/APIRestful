<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerSellerController extends ApiController
{

    public function __construct()
    {
          parent::__construct();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
      //obtener los vendedores de un comprador
      /*Nota: -----
        * -En esta ocasion puede que diferentes transaciones tengan diferentes productos pero que esos diferentes productos sean vendidos por el mismo vendedor asi que es posible
         -que despues de manipular la coleccion estemos obteniendo instancias repetidas de estos vendedores por lo cual tenemos que asegurarnos de no estar repitiendo elementos al interior de
         - la lista, para esto utilizaremos el metodo unique() de laravel, como queremos obtener el vendedor de cada producto por eso despues de product ponemos un punto y despues el seller y con
         - esto automaticamente laravel se encarga de resolver las transacciones con la lista de los productos que conforman cada una de estas transacciones y luego el vendedor de cada uno de esos
         - productos y obtenemos la lista con el metodo get(), despues con el metodo pluck ingresamos al producto y luego al vendedor esto se hace por medio de notación de puntos, con esto vamos a
         - obtener la lista de todos los vendedores, ahora es muy posible que en esta lista se repitan los vendedores y para evitarlo usamos el metodo unique() con el id que es unico, el metodo de unique()
         - conserva el valor de los indices originales de la coleccion es decir, es decir si hay un vendedor repetido el que se elimina va a dejar un espacio dentro de la coleccion resultando entonces elementos vacios
         - dentro de nuestra coleccion, para resolverlo utilizamos el metodo values() que lo que hara sera  reorganizar os indices en el orden correcto y eliminar los indices vacios
      */
      $sellers = $buyer->transactions()->with('product.seller')
                  ->get()
                  ->pluck('product.seller')
                  ->unique('id')
                  ->values();
        //dd($sellers);
        return $this->showAll($sellers);
    }

}
