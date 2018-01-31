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
