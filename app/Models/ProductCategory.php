<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Scopes\CompanyBranchScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomAttributeRelations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Intervention\Image\ImageManagerStatic as Image;

class ProductCategory extends Model
{
    use CrudTrait;
    use SoftDeletes;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'product_categories';
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

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyBranchScope);
    }

    /**
     * Get all the childrens categories IDs
     */
    public function getChildrensId($array = [])
    {
        if (!$this->children->count()) {
            return [];
        }

        $array = $this->children->pluck('id')->toArray();

        foreach ($this->children as $children) {
            $aux = $children->getChildrensId();
            $array = array_merge($array, $aux);
        }

        return $array;
    }

    /**
     * Get the qty of products that belongs to a category, including products that belongs to children
     * categories
     * 
     * @param bool $excludeSelf Exclude the parent category products from the count
     * @return int
     */
    public function getProductCount(bool $excludeSelf = false)
    {
        $ids = $this->getChildrensId();
        if (!$excludeSelf) {
            $ids[] = $this->id;
        }
        $result = DB::table('product_category_mapping')->whereIn('product_category_id', $ids)->get();
        $result = $result->unique('product_id');

        return $result->count();
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

    public function parent()
    {
        return $this->belongsTo('App\Models\ProductCategory', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Models\ProductCategory', 'parent_id');
    }
    public function product_class()
    {
        return $this->hasMany('App\Models\ProductClass', 'category_id');
    }

    public function products() 
    {
        return $this->belongsToMany(Product::class, 'product_category_mapping');
    }

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
    public function setImageAttribute($value)
    {
        $attribute_name = 'image';

        $disk = 'public'; 
        
        // if the image was erased
        if ($value == null) {
            // delete the image from disk
            $deleteFile = Str::replaceFirst('/storage/', '', $this->{$attribute_name});
            \Storage::disk($disk)->delete($deleteFile);

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image'))
        {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value.time()) . '.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put('categories/' . $filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            $deleteFile = Str::replaceFirst('/storage/', '', $this->{$attribute_name});
            \Storage::disk($disk)->delete($deleteFile);

            $this->attributes[$attribute_name] = '/storage/categories/' . $filename;
        }
    }
}
