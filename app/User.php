<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    const USUARIO_VERIFICADO = '1';
    const USUARIO_NO_VERIFICADO = '0';

    const USUARIO_ADMINISTRADOR = 'true';
    const USUARIO_REGULAR = 'false';

    protected $table = 'users';

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
