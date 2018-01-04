<?php

use App\User;
use App\Category;
use App\Product;
use App\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Desactivamos temporalmente las claves foraneas
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        User::truncate();
        Category::truncate();
        Product::truncate();
        Transaction::truncate();
        DB::table('category_product')->truncate();

        //definimos la cantidad de registros que vamos a insertar a la bd
        $cantidadUsuarios = 200;
        $cantidadCategorias = 30;
        $cantidadProductos = 1000;
        $cantidadTransacciones = 1000;

        factory(User::class, $cantidadUsuarios)->create();
        factory(Category::class, $cantidadCategorias)->create();

        /*Esta funcion recibe cada uno de los productos, para generear la
        asociación entre los elementos que son muchos a muchos se utiliza
        el metodo attach de laravel, que recibe un array con la lista de
        todos los ids en este caso de las categorias que le vamos a insertar a
        ese producto*/
        factory(Product::class, $cantidadProductos)->create()->each(
          function($producto){
            $categorias = Category::all()->random(mt_rand(1, 5))->pluck('id');
            $producto->categories()->attach($categorias);
          }
        );

        factory(Transaction::class, $cantidadTransacciones)->create();
    }
}
