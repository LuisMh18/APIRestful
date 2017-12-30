<?php

namespace App;
use App\Transaction;

//comprador

//este ya no entiende de Model si no de user, revisar el diagrama de la bd

class Buyer extends User
{
    public function transactions(){
      return $this->hasMany(Transaction::class);
    }
}
