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

        /*Nota para la imagen: obtenemos la imagen de la petición,y laracel sabra automaticamente q es un archivo, al saber q es un archivo nos da acceso a una serie
          de metodos del administrador de archvos de laravel qu nos permitiran gestionar en este caso la imagen, y uno de esos metodos se conoce
          como store() el cual recibe como primer parametro la ubicacion donde vamos a poner en este caso la imagen, y como segundo paramtro opcional
          el sistema de archivos a usar, como tenemos el sistema de archivos de images como el sistema de archivos por defecto entonces no necesitamos
          especificarlo y la ruta se calcula relativamente a la que se establece en el sistema de archivos, entonces por defecto ya habiamos establecido 
          que las imagenes se hiban a insertar en la carpeta img de la carpeta public asi q no necesitamos definir nngun parametro adicional para la ruta,
          de este modo laravel se encargara de almacenar automaticamente la imagen y lo mas importante es q generara automaticamente un nombre aleatorio y
          unico para esa imagen, asi q no nos tenemos que preocupar por establecerle un nombre a la imagen ps laravel lo ara por nosotros */
        $data['status'] = Product::PRODUCTO_NO_DISPONIBLE;//por defecto es estado de un producto es no disponible
        $data['image'] = $request->image->store('');
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

        //llamamos al metodo para verificar al vendedor
        $this->verificarVendedor($seller, $product);


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
        /*eliminarr un producto de un vendedor especifico
         *para esto tendremos que asegurarnos de que realmente el id del vendedor que se especifique en la url sea el id del vendedor de ese producto especifo basicamente lo que hicimos en la actualización
        */

        //llamamos al metodo para verificar al vendedor
        $this->verificarVendedor($seller, $product);

        /*Para eliminar archivos se utiliza Storage que basicamente nos permite interactuar directamente con el sistema de archivos de laravel
          una vez mas podemos especidficr el sistema de archivos sin embargo si no lo especificamos laravel utilizara el sistema de archivos
          por defecto que en este caso es el de images, el metodo delete recibira unicamente el nombre o la ruta completa relativa al sistema de
          aechivos donde se encuentra el archivo a eliminar, en este caso como sabemos que todas las rutas del sistema de archivos images son relativas
          a la carpeta public/img  solo tendriamos que especficar el nombre del archivo ocomo tal*/
        Storage::delete($product->image);

        $product->delete();

        return $this->showOne($product);
    }

    //metodo para verificar el vendedor
    protected function verificarVendedor(Seller $seller, Product $product)
    {
        //verificamos si el id del vendedor que recibimos en la peticion es el mismo id del vendedor asociado a ese producto
        //en caso de que no sea el mismo tenemos que retornar un error
        if ($seller->id != $product->seller_id) {
            throw new HttpException(422, 'El vendedor especificado no es el vendedor real del producto');
        }
    }
}
