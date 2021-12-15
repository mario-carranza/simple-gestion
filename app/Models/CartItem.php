<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'cart_items';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function updateTotals()
    {
        $this->sub_total = $this->price * $this->qty;
        $this->total = $this->price * $this->qty; //@todo calculate total
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function parent()
    {
        return $this->belongsTo(Product::class);
    }

    public function product_reservation()
    {
        return $this->belongsTo(ProductReservation::class);
    }

    /*
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }*/

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
