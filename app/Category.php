<?php

namespace App;
use App\Product;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

use App\Transformers\CategoryTransformer;

class Category extends Model
{

  use SoftDeletes;

  //relacionamos el modelo con su respectiva transformación
  public $transformer = CategoryTransformer::class;

  protected $dates = ['deleted_at'];

  //atributos que pueden ser asignados de manera masiva, una asignasion masiva de manera masiva en laravel cuando se realiza
  //el establecimiento de tal atributo por medio del metodo create o update
    protected $fillable = [
      'name',
      'description'
    ];

    //le solicitamos que oculte el atributo pivot, osea que lo exluda de los resiltados
    protected $hidden = [
      'pivot'
    ];

    public function products(){
      return $this->belongsToMany(Product::class);
    }
}
