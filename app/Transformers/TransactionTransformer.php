<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Transaction;

class TransactionTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Transaction $transaction)
    {
        return [
            'identificador' => (int)$transaction->id,
            'cantidad' => (int)$transaction->quantity,
            'comprador' => (int)$transaction->buyer_id,
            'producto' => (int)$transaction->product_id,
            'fechaCreacion' => (string)$transaction->created_at,
            'fechaActualizacion' => (string)$transaction->updated_at,
            'fechaEliminacion' => isset($transaction->deleted_at) ? (string) $transaction->deleted_at : null,
            /* HATEOAS ------------------
             * son basicamente una manera de mejorar la navegación y la información para la apirestful, estan 
             * especiamente diseados para las maquinas que estan consumiendo apirestful,por medio de hateoas
             * podemos generrar enlaces entre diferentes recursos de la apirestful, por ejemplo un enlace 
             * hacia la lista de categorias en las que ese comprador a reaizado alguna compra, etc.
             * en porcas palabras es una especie de navegación en donde puedes acceder a diferentes partes de 
             * apirestful por supuesto relacionandolo como tal al recurso que se esta utilizando o consumiendo
             * en ese momento.
             */
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('transactions.show', $transaction->id),
                ],
                [
                    'rel' => 'transaction.categories',
                    'href' => route('transactions.categories.index', $transaction->id),
                ],
                [
                    'rel' => 'transaction.seller',
                    'href' => route('transactions.sellers.index', $transaction->id),
                ],
                [
                    'rel' => 'buyer',
                    'href' => route('buyers.show', $transaction->buyer_id),
                ],
                [
                    'rel' => 'product',
                    'href' => route('products.show', $transaction->product_id),
                ],
            ],
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'identificador' => 'id',
            'cantidad' => 'quantity',
            'comprador' => 'buyer_id',
            'producto' => 'product_id',
            'fechaCreacion' => 'created_at',
            'fechaActualizacion' => 'updated_at',
            'fechaEliminacion' => 'deleted_at',
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
