<?php
namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponser{
  //metodo para los mensajes success
  public function successResponse($data, $code){
      return response()->json($data, $code);
  }

  //metodo para los mensajes de error
  public function errorResponse($message, $code){
      return response()->json(['error' => $message, 'code' => $code], $code);
  }

  //metodo para mostrar una respuesta de elementos
  protected function showAll(Collection $collection, $code = 200)
	{
    //verificamos si la colección esta vacia
		if ($collection->isEmpty()) {
			return $this->successResponse(['data' => $collection], $code);
		}

    $transformer = $collection->first()->transformer;
    $collection = $this->filterData($collection, $transformer);//filtrado
    $collection = $this->sortData($collection, $transformer);//ordenación
    $collection = $this->transformData($collection, $transformer);

		return $this->successResponse($collection, $code);
	}

  //metodo que mostrara una instancia especifica, por ejemplo cuando tenemos una instancia de un usuario existente
  public function showOne(Model $instance, $code = 200){
    $transformer = $instance->transformer;
    $instance = $this->transformData($instance, $transformer);

    return $this->successResponse($instance, $code);
  }

  
  public function showMessage($message, $code = 200){
    return $this->successResponse(['data' => $message], $code);
  }


  /* metodo para filtrar por multiples atributos
   * recibe la colección de datos  y la transformación para poder identificar el atributo por el cual se va hacer el filtrado
   * entonces obteneos la lista de todos los attributos que recibimos como parametro por medio de la url y hacemos un foreach
   * para recorrer una a una verificar si ese parametro es un atributo original del modelo que adicionalmente tenga un valor y 
   * en caso de que ambas cosas sean ciertas realizar el filtrado utilizando el metodo where
   */
  protected function filterData(Collection $collection, $transformer)
	{
		foreach (request()->query() as $query => $value) {
			$attribute = $transformer::originalAttribute($query);

			if (isset($attribute, $value)) {
				$collection = $collection->where($attribute, $value);
			}
		}

		return $collection;
	}


  /* metodo para la ordenación de resultados segun el cliente lo desee ya se por nombre, correo, etc
   * recibe la colección de datos, y la transformación para poder identificar el atributo por el cual se va hacer la ordenación
   * la ordenación se ordenara unicamente si recibimos un prametro en la url llamado sort_by, asi que primero
   * verificamos si viene este atributo, si existe entonces obtenemos su valor para ordenarlo, por suerte como estaos haciendo uso de las
   * colecciones de laravel tenemos aceso a multiples metodos lo cual incluye un metodo para ordenar la colección como tal que es el
   * metodo sortBy el cual recibe el atributo q utilizaremos para ordenar, entonces obtenemos el atributo mandado que tiene que ser el 
   * atributo como esta en la transformación y llamamos al metodo originalAttribute para comparar con el atributo original de la bd
   */
  protected function sortData(Collection $collection, $transformer)
	{
		if (request()->has('sort_by')) {
			$attribute = $transformer::originalAttribute(request()->sort_by);

			$collection = $collection->sortBy->{$attribute};
		}
		return $collection;
	}

  /* metodo para las transformaciones
    *recice dos cosas, el primero es la información a transformar y la segunda es la instancia o la clase que se utiizara
    *para transformar esos datos
   */
  protected function transformData($data, $transformer)
	{
		$transformation = fractal($data, new $transformer); //construimos las transformación

		return $transformation->toArray();//convertimos la transformación en un array
	}

}


 ?>
