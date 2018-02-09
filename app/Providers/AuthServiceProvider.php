<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\User;
use App\Buyer;
use App\Seller;
use App\Product;
use Carbon\Carbon;
use App\Transaction;
use App\Policies\UserPolicy;
use App\Policies\BuyerPolicy;
use App\Policies\SellerPolicy;
use Laravel\Passport\Passport;
use App\Policies\ProductPolicy;
use App\Policies\TransactionPolicy;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $this->registerPolicies();

        Gate::define('admin-action', function ($user) {
            return $user->esAdministrador();
        });

        Passport::routes();
        /* fecha de expiracion del token, a partir de que se genere el token, 
        obtenemos la fecha exacta de ese momento por medio de la libreria Carbon y el metodo now, y en este caso le decimos que el token sera
        valido por 30minutos solamente*/
        Passport::tokensExpireIn(Carbon::now()->addMinutes(30)); 
        /*Ahora como el token expira tan pronto es muy prbable que el cliente requiera un refreshToken para generar un nuevo token a partir 
          de el que ya expiro pero por supuesto no queremos que pueda generar nuevos tokens despues de mucho tiempo asi que también vamos
          agregarle un tiempo de expiración pero en este caso sera mucho mayor, en este caso sera valido durante 30 días, esto quiere decir entonces que una
          vez que el token original expira el cliente tiene maximo 30 dias para utilizar un refreshToken y obtener uno nuevo, si no despues de esa fecha tendria
          que realizar nuevamente el flujo de autorización para obtener un nuevo token por parte del usuario*/
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
    //    Passport::enableImplicitGrant();

      /*  Passport::tokensCan([
            'purchase-product' => 'Crear transacciones para comprar productos determinados',
            'manage-products' => 'Crear, ver, actualizar y eliminar productos',
            'manage-account' => 'Obtener la informacion de la cuenta, nombre, email, estado (sin contraseña), modificar datos como email, nombre y contraseña. No puede eliminar la cuenta',
            'read-general' => 'Obtener información general, categorías donde se compra y se vende, productos vendidos o comprados, transacciones, compras y ventas',
        ]);*/
    }
}
