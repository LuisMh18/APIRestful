<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreated;

use App\User;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        //con el metodo all accedemos a la lista de todos los usuarios disponibles
        $usuarios = User::all(); 

        return $this->showAll($usuarios);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        //reglas de validacion
        $reglas = [
          'name' => 'required',
          'email' => 'required|email|unique:users',//el email debe de ser unico en la tabla usuarios
          'password' => 'required|min:6|confirmed',//la coontrasea debe de ser confirmada con un campo llamado password_confirmation
        ];

        $this->validate($request, $reglas);

        //el $request->all(); obtiene todos los datos del formulario con los campos correspondientes, en este caso del usuario
        $campos = $request->all();
        $campos['password'] = bcrypt($request->password);//encriptamos la contrasea
        $campos['verified'] = User::USUARIO_NO_VERIFICADO;//por defecto el usuario es no verificado porq eso lo cambia el admin
        $campos['verification_token'] = User::generarVerificationToken();//llamamos al matodo para generar el token
        $campos['admin'] = User::USUARIO_REGULAR;//por defecto el usuario es regular porq eso lo cambia el admin
        $usuario = User::create($campos);

        return $this->showOne($usuario, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /*Usando inyección de Modelos,
     *con esto ya no es necesario estar buscando por id, si no que simplemente pasandole el modelo como parametro
     a la función este ya nos hace la busqueda correspondiente como si lo hicieramos con el findOrFail asi,  User::findOrFail($id);
     */
    public function show(User $user){
      //$user = User::find($id);

      //si en caso de que lo que se busca no exista para esp se usa el metodo findOrFail
      //$user = User::findOrFail($id);

      return $this->showOne($user);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user){
      //$user = User::findOrFail($id);

      $reglas = [
        'email' => 'email|unique:users,email,' . $user->id,//validamos que el email pueda ser el mismo del usuario actual, es decir el email debe de ser unico pero puede quedar con el mismo valor si es q no es modificado
        'password' => 'min:6|confirmed',
        'admin' => 'in:'.User::USUARIO_ADMINISTRADOR.','.User::USUARIO_REGULAR,//verificamos que el valor de admin este incluido en uno de estos dos posibles valores
      ];

      $this->validate($request, $reglas);

      //mediante el metodo has verificamos que tengamos un campo con el nombre asignado, en este caso es
      //el campo name, y si este viene entonces lo actualizamos
      if($request->has('name')){
        $user->name = $request->name;
      }

      //comprobamos si el email es diferente al q el usuario tiene actualmente
      //en caso de que sea asi sra un usuario no verificado y tenemos que asignarle un nuevo token
      if($request->has('email') && $user->email != $request->email){
          $user->verified = User::USUARIO_NO_VERIFICADO;
          $user->verification_token = User::generarVerificationToken();
          $user->email = $request->email;
      }

       //validamos que un usuario pueda convertirse en administrador unicamente si es ya un usuario verificado
       if($request->has('admin')){
         //si el usuario no es verificado mandamos un msj de error
         if(!$user->esVerificado()){
           return $this->errorResponse('Unicamente los usuarios verificados pueden cambiar su valor de administrador', 409);
         }

         //en caso de que si sea un usuario verificado
          $user->admin = $request->admin;
       }

       //el metodo isDirty valida si algunos e los valores originales ah cambiado su valor
       if(!$user->isDirty()){
         return $this->errorResponse('Se debe de especificar un valor diferente para actualizar', 422);
       }

       $user->save();

       return $this->showOne($user);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user){
        //$user = User::findOrFail($id);
        $user->delete();

        return $this->showOne($user);
    }

    //ruta para la verificación de usuarios.
    public function verify($token)
    {
        //buscamos al usuario cuyo token de verificación sea igual al que recibimos
        $user = User::where('verification_token', $token)->firstOrFail();
        //ya que sabemos que existe el usuario procedemos a verificarlo
        $user->verified = User::USUARIO_VERIFICADO;
        $user->verification_token = null;//para evitar que el usuario pueda seguir utilizando este mismo token para intentar seguir validando su cuenta de manera inecesaria removemos el token de verificación actual

        $user->save();

        return $this->showMessage('La cuenta ha sido verificada');
    }

    public function resend(User $user)
    {
        //verificamos que la cuenta ya no sea de un usuario verificado
        if ($user->esVerificado()) {
            return $this->errorResponse('Este usuario ya ha sido verificado.', 409);
        }

        //en caso de que no haya sido verificado procedemos ah enviar nuevamente el correo
        //retry(5, function() use ($user) {
            Mail::to($user)->send(new UserCreated($user));
        //}, 100);

        return $this->showMessage('El correo de verificación se ha reenviado');

    }
}
