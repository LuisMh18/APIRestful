<?php

namespace App;
use App\Product;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{

  use SoftDeletes;

  protected $dates = ['deleted_at'];

  //atributos que pueden ser asignados de manera masiva, una asignasion masiva de manera masiva en laravel cuando se realiza
  //el establecimiento de tal atributo por medio del metodo create o update
    protected $fillable = [
      'name',
      'description'
    ];

    public function products(){
      return $this->belongsToMany(Product::class);
    }
}
