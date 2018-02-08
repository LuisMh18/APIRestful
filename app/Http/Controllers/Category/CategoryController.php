<?php

namespace App\Http\Controllers\Category;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Transformers\CategoryTransformer;

class CategoryController extends ApiController
{

  public function __construct()
    {
       // $this->middleware('client.credentials')->only(['index', 'show']);
       // $this->middleware('auth:api')->except(['index', 'show']);
        $this->middleware('transform.input:' . CategoryTransformer::class)->only(['store', 'update']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        return $this->showAll($categories);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      //reglas de validacion
      $reglas = [
        'name' => 'required',
        'description' => 'required',
      ];

      $this->validate($request, $reglas);

      $category = Category::create($request->all());

      return $this->showOne($category, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $this->showOne($category);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        //el metodo fill recibe los valores que vamos actualizar
        //el metodo insersect recibe unicamente los valores requeridos en este caso es nam e y description
        $category->fill($request->intersect([
          'name',
          'description',
        ]));

        /*comprobamos si la categoria cambio alguno de sus valores respecto a la instancia original y esto se hace por el
          metodo isClean() que verifica si la instancia no ah cambiado*/
          if($category->isClean()){
            return $this->errorResponse('Debe especificar al menos un valor diferente para actualizar', 422);
          }

          $category->save();

          return $this->showOne($category);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->showOne($category);
    }
}
