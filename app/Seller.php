<?php

namespace App;
use App\Product;

//vendedor

//este ya no entiende de Model si no de user, revisar el diagrama de la bd

class Seller extends User
{
  public function products(){
    return $this->hasMany(Product::class);
  }
}
