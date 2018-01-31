<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Transformers\UserTransformer;

/* Nota: SoftDeleting sirve para no remover completamente la instancia(registro) si no 
   para ocultarla partiendo de la existencia de esa fecha, basicamente si la fecha existe laravel lo
   oculta y si no existe lo muestra con normalidad*/

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    const USUARIO_VERIFICADO = '1';
    const USUARIO_NO_VERIFICADO = '0';

    const USUARIO_ADMINISTRADOR = 'true';
    const USUARIO_REGULAR = 'false';

    //relacionamos el modelo con su respectiva transformación
    public $transformer = UserTransformer::class;

    protected $table = 'users';
    protected $dates = ['deleted_at'];//le especificamos que el campo deleted_at sera tratado como una fecha

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verified',
        'verification_token',
        'admin',
    ];

    /*
     * - Mutadores y accesores en los modeo
     * - Son metodos que se implementan en los modelos para la modificación de un atributo y  para acceder a dicho valor
     *
     * -- Un mutador se utiliza para modificar el valor original de un atributo antes de hacer la insercion en la base de datos
     *
     *-- El accesor se utiliza para modificar el valor de un atributo despuesn de haberlo obtenido de la base de datos
    */

    /*Antes de insertar el nombre requiere que todos los caracteres esten en minuscula excepto el inicial, entonces en este
      caso vamos a usur un mutador y un accesor*/
      //mutador
      public function setNameAttribute($valor){
        $this->attributes['name'] = strtolower($valor);//para que el nombre siempre se inserte en minuscula
      }

      //accesor
      public function getNameAttribute($valor){
        //con esto estamos retornando el valor del nombre siempre con la primera letra de cada palabra en mayuscula sin la necesidad de modificarlo en la bd
        return ucwords($valor);
      }



      /*Para el correo electronico solo vamos a poner todo en minuscula por lo cual solo usaremos un mutador */
      public function setEmailAttribute($valor){
        $this->attributes['email'] = strtolower($valor);//para que el email siempre se inserte en minuscula
      }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    //atributos ocultos
    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    //metodo para generar el token de verificación del usuario
    public function esVerificado(){
      return $this->verified == User::USUARIO_VERIFICADO;
    }

    //metodo para saber si el usuario es admin
    public function esAdministrador(){
      return $this->admin == User::USUARIO_ADMINISTRADOR;
    }

    //metodo estatico q nos permitira obtener un token de verificacion generado automaticamente
    public static function generarVerificationToken(){
      return str_random(40);
    }
}
