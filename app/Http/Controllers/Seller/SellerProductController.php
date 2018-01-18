<?php

namespace App\Http\Controllers\Seller;

use App\User;
use App\Seller;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;
use App\Transformers\ProductTransformer;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
  /*  public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:' . ProductTransformer::class)->only(['store', 'update']);

        $this->middleware('scope:manage-products')->except('index');
        $this->middleware('can:view,seller')->only('index');
        $this->middleware('can:sale,seller')->only('store');
        $this->middleware('can:edit-product,seller')->only('update');
        $this->middleware('can:delete-product,seller')->only('destroy');
    }*/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
   /* public function index(Seller $seller)
    {
        if (request()->user()->tokenCan('read-general') || request()->user()->tokenCan('manage-products')) {
            $products = $seller->products;

            return $this->showAll($products);
        }

        throw new AuthenticationException;
        
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
                                            //recivimos User $seller para que si el usuario(vendedor) va a publicar un producto por primera vez ,porq si no solamente esrarias reciviendo instancias de usuarios que tengan por lo menos un producto
    public function store(Request $request, User $seller)
    {
        //crear producto para un usuario(vendedor)
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'quantity' => 'required|integer|min:1',
            'image' => 'required|image',
        ];

        $this->validate($request, $rules);

        $data = $request->all();

        $data['status'] = Product::PRODUCTO_NO_DISPONIBLE;//por defecto es estado de un producto es no disponible
        $data['image'] = '1.jpg'; //$request->image->store('');
        $data['seller_id'] = $seller->id;

        $product = Product::create($data);

        return $this->showOne($product, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Seller $seller, Product $product)
    {
        /*actualizar un producto de un vendedor especifico
         *para esto nos tenemos que asegurar de que el vendedor que se especifico en la url sea verdaderamente el propietario de dicho producto y una 
         *serie de restricciones adicionales, por ejemplo antes de actualizar el estado de u producto de no disponible a disponible este deba de tener 
         *por lo menos una categoria  
        */

        $rules = [
            'quantity' => 'integer|min:1',
            'status' => 'in: ' . Product::PRODUCTO_DISPONIBLE . ',' . Product::PRODUCTO_NO_DISPONIBLE,
            'image' => 'image',
        ];

        $this->validate($request, $rules);

        //verificamos si el id del vendedor que recibimos en la peticion es el mismo id del vendedor asociado a ese producto
        //en caso de que no sea el mismo tenemos que retornar un error
        if($seller->id != $product->seller_id){
            return $this->errorResponse('El vendedor especificado no es el vendedor real del producto', 402);
        }

       // $this->verificarVendedor($seller, $product);

       //llenamos las primeras instancias de la actualización utilizando el metodo fill
        $product->fill($request->intersect([
            'name',
            'description',
            'quantity',
        ]));

        if ($request->has('status')) {
            $product->status = $request->status;//vamos a modificar el estado de manera incial, lo hacemos de manera inicial porq el estado q recibimos puede ser tanto disponible como no disponible 

            //si el produto esta disponible y la cantidad de categorias de ese producto es igual a 0, entonces rtornamos un error
            if ($product->estaDisponible() && $product->categories()->count() == 0) {
                return $this->errorResponse('Un producto activo debe tener al menos una categoría', 409); //esto es u conflicto y por eso utiizamos el codigo de estado 409
            }
        }

       /* if ($request->hasFile('image')) {
            Storage::delete($product->image);

            $product->image = $request->image->store('');
        }*/


        if ($product->isClean()) {
            return $this->errorResponse('Se debe especificar al menos un valor diferente para actualizar', 422);
        }

        $product->save();

        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        $this->verificarVendedor($seller, $product);

        Storage::delete($product->image);

        $product->delete();

        return $this->showOne($product);
    }

    protected function verificarVendedor(Seller $seller, Product $product)
    {
        if ($seller->id != $product->seller_id) {
            throw new HttpException(422, 'El vendedor especificado no es el vendedor real del producto');
        }
    }
}
