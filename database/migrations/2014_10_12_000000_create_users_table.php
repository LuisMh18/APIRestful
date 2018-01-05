<?php

use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            //le agregamos un valor por defecto que el usuario sera no verificado de manera predeterminada
            $table->string('verified')->default(User::USUARIO_NO_VERIFICADO);
            //leagregamos la opcion de q sea nulo
            $table->string('verification_token')->nullable();
            //por defecto sera un usurio regular
            $table->string('admin')->default(User::USUARIO_REGULAR);
            $table->timestamps();
            $table->softDeletes();/*Metodo que agregara una nueva fecha que es la fecha de eliminación del modelo en caso de que este haya sido eliminado*/
            /* Nota: SoftDeleting sirve para no remover completamente la instancia(registro) si no
               para ocultarla partiendo de la existencia de esa fecha, basicamente si la fecha existe laravel lo
               oculta y si no existe lo muestra con normalidad*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
