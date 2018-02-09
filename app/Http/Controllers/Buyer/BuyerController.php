<?php

namespace App\Http\Controllers\Buyer;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Buyer;

class BuyerController extends ApiController
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
    public function index(){
        /*obtenemos los compradores, unicamente los que tengan compras(Transacciones)
          esto lo hacemos por medio del metodo has() este recibe el nombre de una relación
          que tenga ese modelo, en este caso transactions podemos verificar la relacion en el modelo Buyer
          entonces lo que estamos haciendo es obtener todos kos usuarios que tengan transacciones activas
          es decir los usuarios que son compradores*/

          $compradores = Buyer::has('transactions')
                         ->get();

          return $this->showAll($compradores);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){

      $comprador = Buyer::has('transactions')
                     ->findOrFail($id);

      return $this->showOne($comprador);


    }

}
