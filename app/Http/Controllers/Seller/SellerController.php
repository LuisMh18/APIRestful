<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Seller;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        /*n vendedor es un usuario que tenga porlomenos un producto asociado a el */
        $vendedores = Seller::has('products')
                       ->get();

        return response()->json(['data' => $vendedores], 200);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id){
      $vendedor = Seller::has('products')
                     ->findOrFail($id);

      return response()->json(['data' => $vendedor], 200);
    }


}
