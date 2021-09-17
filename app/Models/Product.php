<?php

namespace App\Models;

use DateTime;
use Exception;
use DateInterval;
use Illuminate\Support\Str;
use App\Scopes\CompanyBranchScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class Product extends Model
{
    use CrudTrait;
    /* use SoftDeletes; */

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'products';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    // protected $dates = [];

    protected $fakeColumns = ['inventories_json'];
    protected $casts = [
        'images_json' => 'array',
        'variations_json' => 'array',
        'attributes_json' => 'array',
        'inventories_json' => 'array',
    ];

    const PRODUCT_TYPE_SIMPLE = 1;
    const PRODUCT_TYPE_CONFIGURABLE = 2;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyBranchScope);
    }

    /**
     * Create or update child products
     *
     * @param array $attributes
     * @return boolean
     */

    public function createUpdateVariations()
    {
        $variations = $this->variations_json;
        $oldVariations = $this->getOriginal('variations_json');

        // Delete old variants
        if (!empty($oldVariations)) {
            foreach ($oldVariations as $oldVariation) {

                $remove = true;

                foreach ($variations as $variation) {
                    if ($oldVariation['product_id'] == $variation['product_id']) {
                        $remove = false;
                    }
                }

                if ($remove) {
                    Product::find($oldVariation['product_id'])->delete();
                }
            }
        }

        // Create or update variants
        foreach ($variations as &$variation) {
            $inventoriesArray = $this->getInventoriesArrayFromVariation($variation);
            $attributesArray = $this->getAttributesArrayFromVariation($variation);

            if (empty($variation['special_price'])) {
                $variation['special_price_from'] = null;
                $variation['special_price_to'] = null;
            }

            // Create if does not exists
            if ($variation['product_id'] == '') {

                $childProduct = Product::create([
                    'sku' => Str::uuid()->toString(), // temporal sku
                    'name' => $this->name,
                    'price' => sanitizeNumber($variation['price']),
                    'special_price' => $variation['special_price'] ? sanitizeNumber($variation['special_price']) : null,
                    'special_price_from' => $variation['special_price_from'] ?: null,
                    'special_price_to' => $variation['special_price_to'] ?: null,
                    'weight' => $this->is_service ? 0 : sanitizeNumber($variation['weight']),
                    'height' => $this->is_service ? 0 : sanitizeNumber($variation['height']),
                    'width' => $this->is_service ? 0 : sanitizeNumber($variation['width']),
                    'depth' => $this->is_service ? 0 : sanitizeNumber($variation['depth']),
                    'inventories_json' => $inventoriesArray,
                    'parent_id' => $this->id,
                    'product_type_id' => self::PRODUCT_TYPE_SIMPLE,
                    'product_class_id' => $this->product_class_id,
                    'product_brand_id' => $this->product_brand_id,
                    'seller_id' => $this->seller_id,
                    'company_id' => $this->company_id,
                    'currency_id' => $this->currency_id,
                    'use_inventory_control' => $this->use_inventory_control,
                    'is_service' => $this->is_service,
                    'is_approved' => $this->is_approved,
                    'status' => $this->status, // always 1?
                ]);

                $childProduct->updateOrCreateAttributes($attributesArray);

                // Store reference to the product in the table
                $variation['product_id'] = $childProduct->id;

                // Otherwise, update
            } else {
                Product::where('id', $variation['product_id'])
                    ->update([
                        'name' => $this->name,
                        'price' => sanitizeNumber($variation['price']),
                        'weight' => $this->is_service ? 0 : sanitizeNumber($variation['weight']),
                        'height' => $this->is_service ? 0 : sanitizeNumber($variation['height']),
                        'width' => $this->is_service ? 0 : sanitizeNumber($variation['width']),
                        'depth' => $this->is_service ? 0 : sanitizeNumber($variation['depth']),
                        'special_price' => $variation['special_price'] ? sanitizeNumber($variation['special_price']) : null,
                        'special_price_from' => $variation['special_price_from'] ?: null,
                        'special_price_to' => $variation['special_price_to']?: null,
                        'inventories_json' => $inventoriesArray,
                        'parent_id' => $this->id,
                        'product_type_id' => self::PRODUCT_TYPE_SIMPLE,
                        'product_brand_id' => $this->product_brand_id,
                        'seller_id' => $this->seller_id,
                        'company_id' => $this->company_id,
                        'currency_id' => $this->currency_id,
                        'use_inventory_control' => $this->use_inventory_control,
                        'is_service' => $this->is_service,
                        'is_approved' => $this->is_approved,
                        'status' => $this->status, // always 1?
                    ]);

                $childProduct = Product::where('id', $variation['product_id'])->firstOrFail();
                $childProduct->updateOrCreateAttributes($attributesArray);
            }

            // Upload children image
            $imagesArray = [];
            $variation['image'] = $this->uploadChildImage($childProduct, $variation['image']);
            array_push($imagesArray, ['image' => $variation['image']]);

            // Update children props
            $childProduct->images_json = $imagesArray;
            $childProduct->sku = $this->sku . '-' . $childProduct->id;
            $childProduct->url_key = $this->url_key . '-' . $childProduct->id;

            // Update chidren categories
            $childProduct->categories()->detach();
            $childProduct->categories()->attach($this->categories->pluck('id'));

            $childProduct->update();
        }

        $this->variations_json = $variations;
    }

    public function getInventoriesArrayFromVariation($variation)
    {
        $inventories = [];

        foreach ($variation as $param => $value) {
            $isAnInventory = substr($param, 0, 16) == 'inventory-source';
            if ($isAnInventory) {
                $inventories[$param] = $value;
            }
        }

        return $inventories;
    }

    public function getAttributesArrayFromVariation($variation)
    {
        $attributes = [];

        foreach ($variation as $param => $value) {
            $isAnAtributte = substr($param, 0, 15) == 'super-attribute';
            if ($isAnAtributte) {
                array_push($attributes, [
                    'id' =>  Str::replaceFirst('super-attribute-', '', $param),
                    'value' => $value,
                ]);
            }
        }

        return $attributes;
    }

    public function createUpdateInventories()
    {
        $inventories = $this->inventories_json;
        $sourceInventories = [];

        // Extract id and qty
        foreach ($inventories as $param => $value) {
            array_push($sourceInventories, [
                'id' =>  Str::replaceFirst('inventory-source-', '', $param),
                'qty' => $value,
            ]);
        }

        foreach ($sourceInventories as $sourceInventory) {
            if (DB::table('product_inventories')->where([
                'product_inventory_source_id' => $sourceInventory['id'],
                'product_id' => $this->id,
            ])->count() === 0) {
                $this->inventories()->attach($sourceInventory['id'], ['qty' => $sourceInventory['qty']]);
            } else {
                $this->inventories()->updateExistingPivot($sourceInventory['id'], ['qty' => $sourceInventory['qty']]);
            }
        }
    }

    /**
     * Create or update the attributes of a product
     *
     * @param array $attributes
     * @return boolean
     */

    public function updateOrCreateAttributes($attributes)
    {
        if (empty($attributes)) return false;

        foreach ($attributes as $attribute) {
            $productAttribute = ProductAttribute::where([
                'product_class_attribute_id' => $attribute['id'],
                'product_id' => $this->id,
            ])->first();

            if (is_null($productAttribute)) {
                $productAttribute = new ProductAttribute();
                $productAttribute->create([
                    'product_class_attribute_id' => $attribute['id'],
                    'product_id' => $this->id,
                    'json_value' => $attribute['value'],
                ]);
            } else {
                $productAttribute->update([
                    'product_class_attribute_id' => $attribute['id'],
                    'product_id' => $this->id,
                    'json_value' => $attribute['value'],
                ]);
            }
        }

        return true;
    }

    /**
     * Remove (files and db references) all images of a product
     *
     */

    public function deleteImages()
    {
        //data
        $disk = 'public';
        $attribute_name = 'images_json';
        $valueDecode = json_decode($this->images_json, true);
        $images = [];

        // no images to delete
        if (empty($valueDecode)) return true;

        // get the img path and store it on an array
        foreach ($valueDecode as $data) {
            array_push($images, $data['image']);
        }

        // delete references to erased images
        foreach ($images as $img) {

            // delete the image from disk
            $deleteFile = Str::replaceFirst('/storage/', '', $img);
            \Storage::disk($disk)->delete($deleteFile);
        }
    }

    /**
     * Upload child products image
     *
     */

    public function uploadChildImage($product, $imgData)
    {
        $attribute_name = 'image';
        $disk = 'public';

        // if the image was erased
        if ($imgData == null) {
            return null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($imgData, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($imgData)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($imgData . time()) . '.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put('products/' . $filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            $deleteFile = Str::replaceFirst('/storage/', '', $this->{$attribute_name});
            \Storage::disk($disk)->delete($deleteFile);

            DB::table('product_images')->insert([
                'product_id' => $product->id,
                'path' => '/storage/products/' . $filename,
            ]);

            return '/storage/products/' . $filename;
        } else {
            return $imgData;
        }
    }

    public function getImages()
    {
        $productImages = DB::table('product_images')->where('product_id', $this->id)->get();

        if (!$productImages->count()) {
            if ($this->parent()->count()) {
                return $this->parent->getImages();
            } else {
                $defaultImage = (object) [ 'path' => '/img/default/default-product-image.png'];
                return [ $defaultImage ];
            }
        }

        return $productImages;
    }

    public function getFirstImagePath()
    {
        $productImage = DB::table('product_images')->where('product_id', $this->id)->first();

        if (!$productImage) {
            if($this->parent()->count()) {
                return $this->parent->getFirstImagePath();
            } else {
                return '/img/default/default-product-image.png';
            }
        }

        return $productImage->path;
    }

    public function getAttributesWithNames()
    {
        $attributes = [];

        foreach ($this->custom_attributes as $custom_attribute) {
            $attribute = ProductClassAttribute::where('id', $custom_attribute->product_class_attribute_id)->first();

            if (!$attribute) continue;

            // Remove empty attributes
            if (!$custom_attribute->json_value) continue;

            array_push($attributes, [
                'name' => $attribute->json_attributes['name'],
                'value' => $custom_attribute->json_value,
            ]);
        }

        return $attributes;
    }

    public function haveSufficientQuantity($qty)
    {
        $total = 0;

        // If the product dont use inventory, just return true
        if (!$this->use_inventory_control) {
            return true;
        }

        // If configurable product, check inventory on children products
        if ($this->product_type->id == self::PRODUCT_TYPE_CONFIGURABLE) {
            $result = false;
            foreach ($this->children as $children) {
                if ($children->haveSufficientQuantity($qty)) $result = true;
            }
            return $result;
        }

        // Total qty on inventories
        foreach ($this->inventories as $inventory) {
            $qtyInventory = $inventory->pivot->qty;
            $total += $qtyInventory;
        }

        // Qty in pending orders
        /* $itemsInOrder = OrderItem::where(['shipping_status' => 1, 'product_id' => $this->id])->get();
        foreach ($itemsInOrder as $orderItem) {
            $total -= $orderItem->qty;
        } */

        return $qty <= $total ? true : false;
    }

    public function updateInventory($qty, $inventorySourceId)
    {
        // Because we also need to store the inventories in an JSON field in order to work with Backpack,
        // and the JSON field update the inventories tables through the Product Oserver we just need
        // to update the JSON field on the Product and the Observer will automatically update the qty

        if (!is_numeric($qty)) {
            throw new Exception('Qty has to be a numeric value');
        }

        if ($qty < 0) {
            throw new Exception('Qty cannot be negative');
        }

        $qty = intval($qty);

        if ($this->parent_id) {
            $parent = Product::find($this->parent_id);
            $variations = $parent->variations_json;
            $position = null;

            foreach ($variations as $key => $variation) {
                if ($variation['product_id'] == $this->id) {
                    $position = $key;
                    break;
                }
            }

            if (!isset($variations[$position]['inventory-source-' . $inventorySourceId])) {
                throw new Exception('The product doesnt have a inventory source with the provide ID');
            }

            $variations[$position]['inventory-source-' . $inventorySourceId] = $qty;
            $parent->variations_json = $variations;
            $parent->update();
        } else {
            $inventories = $this->inventories_json;

            if (!isset($inventories['inventory-source-' . $inventorySourceId])) {
                throw new Exception('The product doesnt have a inventory source with the provide ID');
            }

            $inventories['inventory-source-' . $inventorySourceId] = $qty;
            $this->inventories_json = $inventories;
            $this->update();
        }
    }

    public function showCategory(): string
    {
        return $this->categories()->first()->name;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function getPriceRange()
    {
        if ($this->product_type->id == self::PRODUCT_TYPE_CONFIGURABLE) {
            return [
                $this->children->pluck('price')->sort()->first(),
                $this->children->pluck('price')->sort()->last(),
            ];
        } else {
            return [0, 0];
        }
    }

    public function getRealPriceRange()
    {
        if ($this->product_type->id == self::PRODUCT_TYPE_CONFIGURABLE) {
            return [
                $this->children->pluck('real_price')->sort()->first(),
                $this->children->pluck('real_price')->sort()->last(),
            ];
        } else {
            return [0, 0];
        }
    }

    public function getShippingMethodsByCommune(int $communeId)
    {
        $seller = Seller::find($this->seller_id);
        $communeShippingMethods = $seller->getAvailableShippingMethodsByCommune($communeId);
        $shippingMethods = ShippingMethod::whereIn('code',$communeShippingMethods)->where('status', 1)->get();
        
        if ($this->shipping_methods->count()) {
            $productShippingsId = $this->shipping_methods->pluck('id');
            $shippingMethods = $shippingMethods->whereIn('id', $productShippingsId);
        }

        return $shippingMethods;
    }


    /**
     * Search a product by SKU and Warehouse code. NOTE: This method may fail when there is
     * two sellers with the same product in the same warehouse.
     *
     * @param string $warehouseCode
     * @param string $sku
     * @return Product|null
     */
    public static function getByWarehouseAndSku(string $warehouseCode, string $sku) : ?Product
    {
        $warehouse = ProductInventorySource::where('code', $warehouseCode)->first();

        $productInventory = ProductInventory::where('product_inventory_source_id', $warehouse->id)
                                ->whereHas('product', function ($query) use ($sku)  {
                                   return $query->where('sku', $sku);
                                })->first();
                                
        if (!$productInventory) {
            return null;
        }

        return Product::find($productInventory->product_id);
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function product_class()
    {
        return $this->belongsTo(ProductClass::class);
    }

    public function product_type()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function brand()
    {
        return $this->belongsTo(ProductBrand::class, 'product_brand_id');
    }

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category_mapping');
    }

    public function template()
    {
        return $this->hasMany(Product::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function custom_attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id');
    }

    public function super_attributes()
    {
        return $this->belongsToMany(ProductClassAttribute::class, 'product_super_attributes');
    }

    public function inventories()
    {
        return $this->belongsToMany(ProductInventorySource::class, 'product_inventories', 'product_id', 'product_inventory_source_id')->withPivot('qty');
    }

    public function shipping_methods()
    {
        return $this->belongsToMany(ShippingMethod::class, 'shipping_method_product_mapping');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeBetweenDates($query, $from = null, $to = null)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    public function scopeSearch($query, $q = null)
    {
        return $query->bySeller();
    }

    public function scopeBySeller($query)
    {
        if (!auth()->user() || auth()->user()->hasRole('Super admin')) {
            return $query;
        }

        return $query->where('seller_id', Seller::whereUserId(auth()->user()->id)->first()->id);
    }

    public function scopeByLocation($query)
    {
        if (!request()->session()->has('commune_id')) {
            return $query;
        }
        
        return $query->whereHas('inventories', function ($query2) {
            return $query2->where('commune_id', '=', request()->session()->get('commune_id'));
        });
    }

    public function scopeCanShow($query)
    {
        return $query->where('id', '<>', '')->where('is_approved', '=', 1)->whereHas('seller', function($query) {
            return $query->where('is_approved', '=', 1);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getRealPriceAttribute() {
        if ( is_null($this->special_price) || $this->special_price === 0) {
            return $this->price;
        } else {
            if ( !is_null($this->special_price_from) && !is_null($this->special_price_to) ) {
                $date_now = new DateTime();
                $from  = new DateTime($this->special_price_from);
                $to = new DateTime($this->special_price_to);
                $to->add( new DateInterval('P1D'));

                if( ($date_now < $to) && ($date_now > $from) ) {
                    return $this->special_price;
                } else {
                    return $this->price;
                }
            }

            return $this->special_price;
        }
    }

    public function getHasSpecialPriceAttribute() {

        if ($this->product_type->id == self::PRODUCT_TYPE_CONFIGURABLE && $this->children()->count()) {
            $hasSpecialPrice = false;
            foreach ($this->children as $children) {
                if ($children->has_special_price) $hasSpecialPrice = true;
            }
            return $hasSpecialPrice;
        }

        if ( is_null($this->special_price) || $this->special_price === 0) {
            return false;
        } else {
            if ( !is_null($this->special_price_from) && !is_null($this->special_price_to) ) {
                $date_now = new DateTime();
                $from  = new DateTime($this->special_price_from);
                $to = new DateTime($this->special_price_to);
                $to->add( new DateInterval('P1D'));
                if( ($date_now < $to) && ($date_now > $from) ) {
                    return true;
                } else {
                    return false;
                }
            }

            return true;
        }
    }

    public function getIsApprovedTextAttribute()
    {
        $value = $this->is_approved;

        if (is_null($value) || $value === 2) {
            return 'Pendiente';
        } else if ($value == 1) {
            return 'Aprobado';
        } else {
            return 'Rechazado';
        }
    }

    public function getProductTypeNameAttribute()
    {
        switch ($this->product_type_id) {
            case self::PRODUCT_TYPE_SIMPLE:
                return 'simple';
                break;
            case self::PRODUCT_TYPE_CONFIGURABLE:
                return 'configurable';
                break;
        }
    }

    public function getIsApprovedAttribute($value)
    {
        if (is_null($value)) return 2;

        return $value;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setIsApprovedAttribute($value)
    {
        if ($value == 2) {
            $this->attributes['is_approved'] = null;
            return true;
        }

        $this->attributes['is_approved'] = $value;
    }


    public function setPriceAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['price'] = $value;

            return true;
        }

        $this->attributes['price'] = $this->sanitizeNumber($value);
    }

    public function setSpecialPriceAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['special_price'] = null;

            return true;
        }

        $this->attributes['special_price'] = $this->sanitizeNumber($value);
    }

    public function setCostAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['cost'] = null;

            return true;
        }

        $this->attributes['cost'] = $this->sanitizeNumber($value);
    }

    public function setWidthAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['width'] = null;

            return true;
        }

        $this->attributes['width'] = $this->sanitizeNumber($value);
    }

    public function setHeightAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['height'] = null;

            return true;
        }

        $this->attributes['height'] = $this->sanitizeNumber($value);
    }

    public function setDepthAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['depth'] = null;

            return true;
        }

        $this->attributes['depth'] = $this->sanitizeNumber($value);
    }

    public function setWeightAttribute($value)
    {
        if (is_null($value) || $value === '') {
            $this->attributes['weight'] = null;

            return true;
        }

        $this->attributes['weight'] = $this->sanitizeNumber($value);
    }

    function sanitizeNumber($number)
    {
        //$number = str_replace('.', '', $number);
        $number = str_replace(',', '.', $number);

        return $number;
    }
}
