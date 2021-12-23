<?php

namespace App\Models;

use App\Scopes\CompanyBranchScope;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomAttributeRelations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Support\Str;
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

    public function setFeaturedImageAttribute($value)
    {
        $attribute_name = 'featured_image';

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
