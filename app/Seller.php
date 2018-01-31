<?php

namespace App;
use App\Product;

use App\Transformers\SellerTransformer;

//vendedor

//este ya no entiende de Model si no de user, revisar el diagrama de la bd

class Seller extends User
{

  //relacionamos el modelo con su respectiva transformación
  public $transformer = SellerTransformer::class;

  public function products(){
    return $this->hasMany(Product::class);
  }
}
