<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\User;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'identificador' => (int)$user->id,
            'nombre' => (string)$user->name,
            'correo' => (string)$user->email,
            'esVerificado' => (int)$user->verified,
            'esAdministrador' => ($user->admin === 'true'),
            'fechaCreacion' => (string)$user->created_at,
            'fechaActualizacion' => (string)$user->updated_at,
            'fechaEliminacion' => isset($user->deleted_at) ? (string) $user->deleted_at : null,

            /* HATEOAS ------------------
             * son basicamente una manera de mejorar la navegación y la información para la apirestful, estan 
             * especiamente diseados para las maquinas que estan consumiendo apirestful,por medio de hateoas
             * podemos generrar enlaces entre diferentes recursos de la apirestful, por ejemplo un enlace 
             * hacia la lista de categorias en las que ese comprador a reaizado alguna compra, etc.
             * en porcas palabras es una especie de navegación en donde puedes acceder a diferentes partes de 
             * apirestful por supuesto relacionandolo como tal al recurso que se esta utilizando o consumiendo
             * en ese momento.
             */
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('users.show', $user->id),
                ],
            ],
        ];
    }

    /*metodo que compara los atributos del transformador con los originales a la hora de ordenar resultados por ejemplo,
      para no pasarle los datos reales que serian name, email, etc se usa esta funcion que compara los de la transformacion
      con los atributos originales*/
    public static function originalAttribute($index)
    {
        $attributes = [
            'identificador' => 'id',
            'nombre' => 'name',
            'correo' => 'email',
            'esVerificado' => 'verified',
            'esAdministrador' => 'admin',
            'fechaCreacion' => 'created_at',
            'fechaActualizacion' => 'updated_at',
            'fechaEliminacion' => 'deleted_at',
        ];

        //si esta establecido un atrubuto con el indice que recibimos entonces lo retornamos, de lo contrario retornamos null
        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
