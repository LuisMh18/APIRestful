<?php

namespace App\Providers;

use App\User;
use App\Product;
use App\Mail\UserCreated;
use App\Mail\UserMailChanged;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

         /* Evento para user
          * Cada ves de que se cree un nuevo usuario se le va mandar un correo al usuario
          * El metodo retry() recibe primero cuantas veces queremos que se reintente esa misma accion luegoo recibe la accion que se va a ejecutar
          * y luego recibe cuantos milisegundos van a pasar entr un intento y otro, por ejemplo 100milisegundos, de este modo sabemos que si por alguna razón
          * el envio de correos electronicos falla laravel automaticamente lo va intentar realizar 5 veces mas cada 100milisegundos y q ps si realmente 
          * despues de esas 5 veces sique fallando ps reotrnara la correspondiente excepción
         */
        User::created(function($user) {
            retry(5, function() use ($user) {
                Mail::to($user)->send(new UserCreated($user));
            }, 100);
        });

        /* 
          * Se ejecuta cada vez que una instancia de usuario es actualizada
          * solamnete se va a reenviar el correo electronic si su cuenta de correo original cambio
          * metodo isDirty() nos permite saber si un campo en especifico ah cambiado su valor
         */
        User::updated(function($user) {
            if ($user->isDirty('email')) {
                retry(5, function() use ($user) {
                    Mail::to($user)->send(new UserMailChanged($user));
                }, 100);
            }
        });


        /* Control del estado de un producto dependiendo de su cantidad
          * Lo que queremos hacer es q al momento de que un produto llegue a cantidad de cero su estado cambie 
          * automaticamente a no disponible porsupuesto si su estado estaba aun en disponible, basicamente esto ocurre cuando un
          * producto se ah ido comprando sucesivamente por medio de transacciones hasta que su cantidad llega a cero y en ese momento
          * es cuando queremos que su estado cambie a no disponible, esto lo aremos por eventos del modelo que laravel
          * automaticamente define y controla por nostros, en este caso aremos uso del evento updated
         */
        //decimos que cuando un producto sea actualizado se ejecute lo siguiente
        //si la cantidad es cero y el producto aun esta disponible debemos cambiarle si estado a no disponible
        Product::updated(function($product) {
            if ($product->quantity == 0 && $product->estaDisponible()) {
                $product->status = Product::PRODUCTO_NO_DISPONIBLE;

                $product->save();
            }
        });

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
