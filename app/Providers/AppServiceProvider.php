<?php

namespace App\Providers;

use App\User;
use App\Product;
use App\Mail\UserCreated;
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
         */
        User::created(function($user) {
            //retry(5, function() use ($user) {
                Mail::to($user)->send(new UserCreated($user));
            //}, 100);
        });

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
