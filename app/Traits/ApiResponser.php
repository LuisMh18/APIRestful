<?php
namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

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
    $collection = $this->paginate($collection);//paginación
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
  

   /* metodo para la paginación el cual recibe una colección
   * lo primero que aremos sera entonces resolver la pagina actual, para esto laravel nos proporciona una clase llamada LengthAwarePaginator
   * que es basicamente un paginador que tiene en cuenta el tamao de la colección que nos permite resolver la pagina actual con el
   * metodo resolveCurrentPage , a este punto ya conocemos la pagina en la que estamos, esta pagina sera entonces de gran utilidad para saver cual
   * segmentode la colección vamos a mostrar, le valor predifinido a la cantidad de elementos por pagina, y lo que aremos a continuación sera dividir 
   * la colección completa en diferentes secciónes dependiendo del tamao de la pagina esto lo aremos por medio del metodo slice que es propio de 
   * las colecciones, slice recibe desde que punto hasta que punto vamos a dividir nuestra colección y esto dependera entonces de la pagina actual 
   * en la que nos encontramos, slice nos recibe entonces como primer parametro el primer elemento el cual dependera del numero de pagina en el que nos 
   * encontramos multiplicado por los elementos por pagina, ahora si estamos en la primer pagina entonces el valor deberia de seer cero, entonces a la 
   * pagina tendremos que restarle el valor de 1 y eso lo multiplicamos por la cantidad de elementos por pagina yluego le enviamos la cantidad de elementos
   * que requerimos y obtenemos la coleción, ya que tenemos eso creamos la instancia del paginador como tal, el cual recibe los resultados devidamente
   * divididos segun la pagina y la cantidad, el tamao real de la colección eso lo hacemos por medio del metodo count(), los elementos ppor pagina y una
   * serie de opciones las cuales van en un array, de todas las opciones posibles la unica que realmente nos interesa es la de path que es basicamente la ruta
   * que se utilizara para determinar el recurso actual y porsupuesto la pagina en la que nos encontramos en ese momento, tambien nos permite indicar cual 
   * podria ser la siguiente y cual podria ser la anterior pagina, echo esto ya solo nos queda retornar la colección paginada, es muy importante tener en
   * cuenta que la generación de la ruta de la paginación automaticamente elimina los demas parametros de url que pueda haber alli, es decir si enviamos el
   * numero de pagina y enviamos adicionalmente por ejemplo que queremos ordenar los elementos dependiendo de un atributo, esta ultima parte de ordenacion 
   * de elementos se eliminara lo cual no queremos, entonces para ello simplemente vammos a pedirle al paginador o aos resultados paginados que agreguen
   * la lista de todos los parametros que podamos tener alli
   */
  protected function paginate(Collection $collection)
	{
		/*$rules = [
			'per_page' => 'integer|min:2|max:50'
    ];*/

		//Validator::validate(request()->all(), $rules);

		$page = LengthAwarePaginator::resolveCurrentPage();

		$perPage = 15; //valor predifinido a la cantidad de elementos por pagina
		if (request()->has('per_page')) {
			$perPage = (int) request()->per_page;
		}

		$results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

		$paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
			'path' => LengthAwarePaginator::resolveCurrentPath(),
		]);

		$paginated->appends(request()->all());

		return $paginated;
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
