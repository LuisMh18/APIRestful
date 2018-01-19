<?php

namespace App\Http\Controllers\Product;

use App\User;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Transformers\TransactionTransformer;

class ProductBuyerTransactionController extends ApiController
{
    /*public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:' . TransactionTransformer::class)->only(['store']);
        $this->middleware('scope:purchase-product')->only('store');
        $this->middleware('can:purchase,buyer')->only('store');
    }*/

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        //crear instancias de transacciones
        /*
            * Para esta operación requerimos información de 3 diferentes  recursos que son, el producto, el comprador(usuario) y la transacción
            * Producto -> para saber cual sera el producto que se comprara
            * comprador(usuario) -> para saber quien lo comprara
            * Transacción -> para crear la instancia a partir de la información requerida
        */
        $rules = [
            'quantity' => 'required|integer|min:1',
        ];

        $this->validate($request, $rules);

        //coprobamos que el comprador y el vendedor sean diferentes
        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse('El comprador debe ser diferente al vendedor', 409);
        }

        //verificamos que tanto el comprador como el vendedor sean usuarios verificados
        //comprador
        if (!$buyer->esVerificado()) {
            return $this->errorResponse('El comprador debe ser un usuario verificado', 409);
        }

        //vendedor
        if (!$product->seller->esVerificado()) {
            return $this->errorResponse('El vendedor debe ser un usuario verificado', 409);
        }

        //verificamos que el producto este disponible
        if (!$product->estaDisponible()) {
            return $this->errorResponse('El producto para esta transacción no está disponible', 409);
        }

        //verificamos que l cantidad con la cual se desea crear esa transacción no sea superior a la cantidad disponible del producto
        if ($product->quantity < $request->quantity) {
            return $this->errorResponse('El producto no tiene la cantidad disponible requerida para esta transacción', 409);
        }

        /* Procedemos con la creación de la transacción
            * cabe la posibilidad de que se vayan a generar miltiples transacciones de manera simultanea para un mismo producto, entonces debemos asegurar la disponibilidad
            * del producto para cada transacción, y que en caso contrario retorne el error correspondiente, entonces para asegurarnos de que una transacción se aga despues de
            * la otra y asi sucesivamente sin necesidad de alterar la manera directa de funcionar de nuestro sistema y potr supuesto asegurar que lo que se utiliza de un producto
            * en una transacción se va tener en cuenta para la siguiente vamos a utilizar algo que se conoce como transacciones de la base de datos, basicamente las 
            * transacciones son operaciones qu se realizan completas de a una sola vez una por una y en caso de fallar todo se regresa a su estado normal, es importante tener en 
            * cuenta que esto es para asegurarnos de que estas transacciones se estan construllendo una a una utilizando por supuesto transacciones de la base de datos para esto 
            * laravel nos proporciona un DB que nos da acceso a un metodo llamado transaction la cual recibe una función esta función debe hacer uso de las diferentes instancias
            * en este caso son, el producto, el comprador y la transacción 
        */
        //lo interesante aqui es q si la transaccion falla, ps la transacción nunca se va crear
        return DB::transaction(function () use ($request, $product, $buyer) {
            $product->quantity -= $request->quantity; //reducimos la cantidad disponible del producto
            $product->save();//guardamos el cambio

            //creamos la transaccion despues de haberle reducido la cantidad al producto
            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'buyer_id' => $buyer->id,
                'product_id' => $product->id,
            ]);

            return $this->showOne($transaction, 201);
        });
    }
}
