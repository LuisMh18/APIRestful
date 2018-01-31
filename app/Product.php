<?php

namespace App;
use App\Seller;
use App\Category;
use App\Transaction;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Transformers\ProductTransformer;


class Product extends Model
{

  use SoftDeletes;

    //relacionamos el modelo con su respectiva transformación
    public $transformer = ProductTransformer::class;

  protected $dates = ['deleted_at'];

  const PRODUCTO_DISPONIBLE = 'disponible';
  const PRODUCTO_NO_DISPONIBLE = 'no disponible';

  protected $fillable = [
    'name',
    'description',
    'quantity',
    'status',
    'image',
    'seller_id',
  ];

  //le solicitamos que oculte el atributo pivot, osea que lo exluda de los resiltados
  protected $hidden = [
    'pivot'
  ];

  //funcion para determinar si un producto esta disponible
  public function estaDisponible(){
    return $this->status == Product::PRODUCTO_DISPONIBLE;
  }

  public function seller(){
    return $this->belongsTo(Seller::class);
  }

  public function transactions(){
    return $this->hasMany(Transaction::class);
  }

  public function categories(){
    return $this->belongsToMany(Category::class);
  }


}
