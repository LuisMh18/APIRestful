<?php

namespace App\Http\Controllers\Buyer;

use App\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerTransactionController extends ApiController
{

    public function __construct()
    {
          parent::__construct();
    }

  /*  public function __construct()
    {
        parent::__construct();
        $this->middleware('scope:read-general')->only('index');
        $this->middleware('can:view,buyer')->only('index');
    }*/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Buyer $buyer)
    {
        //obtener la lista de las transacciones de un comprador, osea las compras que tal usuario haya realizado
        $transactions = $buyer->transactions;

        return $this->showAll($transactions);
    }


}
