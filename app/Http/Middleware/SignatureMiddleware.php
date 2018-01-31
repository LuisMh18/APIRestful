<?php

namespace App\Http\Middleware;

use Closure;

/* Los middlewares nos permiten actuar directamente con la petición que recibimos o inclusive sobre la respuesta que vamos a retornar
  *inclusive algunos middlewares tienen la posisbilidad de actuar sobre ambas partes, realizar cambios en la petición tanto como en la respuesta
  *también hay middlewares para validaciones, por ejemplo para verificar cuando un usuario esta autenticado y para muchas otras cosas
  *middleware que se encarga de agregar una cabecera con el nombre de la aplicación en cada una de las respuestas que damos
  *Existen diferentes formas de ejecutar un middleware y es si queremos que el middleware se ejecute antes de cuaquier otro o antes
  *inclusive de haberse generado una respuesta se cononce como before middleware, oh despues de haberse generado una respuesta y 
  *actuar sobre ella after middleware, en este caso como lo que
  *queremos es agregar una cabecera a la respuesta entonces primero debeos contruir la respuesta y luego actuar sobre ella crearemos un after middleware
  *como es un after middleware primero llamamos al metodo $next pero si fuera un before middleware primero tendriamos que contruir todo el codigo y hasta
  *el ultimo llamar al metodo $next
  *los middlewares se ejecutan en el archivo kernel.php de la carpeta Middleware
*/

class SignatureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $header = 'X-Name')
    {
        $response = $next($request);
        $response->headers->set($header, config('app.name'));
        return $response; 
    }
}
