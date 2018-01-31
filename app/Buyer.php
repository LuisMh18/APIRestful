<?php

namespace App;
use App\Transaction;

use App\Transformers\BuyerTransformer;

//comprador

//este ya no entiende de Model si no de user, revisar el diagrama de la bd

class Buyer extends User
{

    //relacionamos el modelo con su respectiva transformación
    public $transformer = BuyerTransformer::class;

    public function transactions(){
      return $this->hasMany(Transaction::class);
    }
}
