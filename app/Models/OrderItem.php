<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Support\Carbon;


class OrderItem extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'order_items';
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

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function order()
    {
        return $this->belongsTo(Order::class);
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

    /*
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
*/
    public function shipping()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function product_reservation()
    {
        return $this->belongsTo(ProductReservation::class);
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeSearch($query, $q = null)
    {
        return $query
            ->sold()
            ->bySeller();
    }

    public function scopeBetweenDates($query, $from = null, $to = null)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeBySeller($query)
    {
        if (!auth()->user() || auth()->user()->hasRole('Super admin') || auth()->user()->can('dashboard.admin')) {
            return $query;
        }

        return $query->where('seller_id', Seller::whereUserId(auth()->user()->id)->first()->id);
    }

    public function scopeMostPurchased($query)
    {
        return $query->orderByDesc('qty')->limit(3);
    }

    public function scopeSold($query)
    {
        return $query->whereHas('order', function ($q) {
            return $q->sold();
        });
    }


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
