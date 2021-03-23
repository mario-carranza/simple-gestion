<?php

namespace App\Models;

use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Seller;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
     */
    const STATUS_INITIATED = 1;
    const STATUS_PAID = 2;
    const STATUS_COMPLETED = 3;

    protected $table = 'orders';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['json_value'];
    // protected $hidden = [];
    // protected $dates = [];
    protected $casts = [
        'json_value' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
     */
    public function getSellers()
    {
        $products_id = OrderItem::whereOrderId($this->id)->select('product_id')->with('product')->get();
        foreach ($products_id as $id) {
            $ids[] = $id['product_id'];
        }

        if (count($products_id) > 0) {
            $sellers_id = Product::whereIn('id', $ids)->select('seller_id')->groupBy('seller_id')->get();
            return Seller::whereIn('id', $sellers_id)->select('id', 'name', 'email')->get();
        } else {
            return null;
        }

    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
     */
    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function order_payments()
    {
        return $this->hasMany(OrderPayment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_order');
    }    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
     */

    public function scopeSearch($query, $q = null)
    {
        return $query->bySeller();
    }

    public function scopeSold($query)
    {
        return $query->whereIn('status', [self::STATUS_PAID, self::STATUS_COMPLETED]);
    }

    public function scopeBySeller($query)
    {
        $sellerUid = Seller::whereUserId(auth()->user()->id)->first();
        if (!auth()->user() || auth()->user()->hasRole('Super admin') || !isset($sellerUid)) {
            return $query;
        }

        return $query->whereHas('order_items', function ($query) use ($sellerUid) {
            $query->where('seller_id', $sellerUid->id);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
     */

    public function getStatusDescriptionAttribute()
    {
        switch ($this->status) {
            case $this::STATUS_INITIATED:
                return 'Pendiente';
                break;
            case $this::STATUS_PAID:
                return 'Pagada';
                break;
            case $this::STATUS_COMPLETED:
                return 'Completa';
                break;
            default:
                break;
        }
    }

    public function getJsonValueAttribute()
    {
        if ($this->attributes) {
            $addressData = [];
            $attribute_name = 'json_value';
            $tmpAddressData = json_decode($this->attributes[$attribute_name]);
            $tmpAddressData = is_object($tmpAddressData)
            ? $tmpAddressData
            : json_decode($tmpAddressData);
            if(!is_null($tmpAddressData)) {
                $item = [
                    'addressShipping' => json_decode($tmpAddressData->addressShipping),
                    'addressInvoice' => json_decode($tmpAddressData->addressInvoice),
                ];
                $addressData[] = $item;

                return $item;
            } else {
                return null;
            }
        }
    }

    public function getOrderItemsAttribute()
    {
        if (backpack_user()) {
            if (backpack_user()->hasRole('Vendedor marketplace')) {
                $items = [];
                $userSeller = Seller::where('user_id', backpack_user()->id)->firstOrFail();
                $tmpitems = $this->order_items()->get();

                foreach ($tmpitems as $item) {
                    if ($item->product->seller_id == $userSeller->id) {
                        $items[] = $item;
                    }
                }
                return collect($items);
            } else {
                return $this->order_items()->get();
            }
        } else {

            return $this->order_items()->get();
        }

    }

    public function getInvoiceAttribute()
    {
        return $this->invoices->first();
    }

    /*
|--------------------------------------------------------------------------
| MUTATORS
|--------------------------------------------------------------------------
 */

}
