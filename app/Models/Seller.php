<?php

namespace App\Models;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Scopes\CompanyBranchScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomAttributeRelations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Freshwork\ChileanBundle\Rut;
use Intervention\Image\ImageManagerStatic as Image;

class Seller extends Model
{
    use CrudTrait;
    use CustomAttributeRelations;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    const REVIEW_STATUS_PENDING = 0;
    const REVIEW_STATUS_APPROVED = 1;
    const REVIEW_STATUS_REJECTED = 2;

    protected $table = 'sellers';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = [
        'uid',
        'name',
        'visible_name',
        'description',
        'email',
        'phone',
        'cellphone',
        'web',
        'password',
        'notes',
        'legal_representative_name',
        'custom_1',
        'custom_2',
        'addresses_data',
        'activities_data',
        'payments_data',
        'shippings_data',
        'banks_data',
        'contacts_data',
        'subscription_data',
        'is_approved',
        'rejected_reason',
        'source',
        'status',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'return_policy',
        'shipping_policy',
        'privacy_policy',
        'commission_enable',
        'commission_percentage',
        'logo',
        'banner',
        'styles_json',
        'seller_category_id',
        'user_id',
        'company_id',
        'slug',
        'maximun_days_for_shipped',
    ];

    protected $hidden = [
        'password'
    ];

    // protected $dates = [];
    protected $casts = [
        'addresses_data' => 'array',
        'activities_data' => 'array',
        'banks_data' => 'array',
        'contacts_data' => 'array',
        'subdcription_data' => 'array',
    ];
    protected $fakeColumns = [
        'subscription_data'
    ];

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

    public function getReviewStatus()
    {
        switch ($this->is_approved) {
            case $this::REVIEW_STATUS_APPROVED:
                return 'Aprobado';
                break;
            case $this::REVIEW_STATUS_REJECTED:
                return 'Rechazado';
                break;
            default:
                return 'Pendiente'; //REVIEW_STATUS_PENDING
                break;
        }
    }

    /**
     * Return an array with the codes of the available shipping methods
     */
    public function getAvailableShippingMethodsByCommune($communeId)
    {
        $shippingConfig = CommuneShippingMethod::where([ 'seller_id' => $this->id, 'commune_id' => $communeId ])->first();

        if (!$shippingConfig) {
            $shippingConfig = CommuneShippingMethod::where([ 'seller_id' => $this->id, 'is_global' => 1 ])->first();

            if (!$shippingConfig) {
                return [];
            }

            return $shippingConfig->getAvailableShippingMethodCodes();
        }

        return $shippingConfig->getAvailableShippingMethodCodes();
    }

    public function getResumeAvailableShippingMethods()
    {
        $availableShippingMethods = CommuneShippingMethod::where([ 'seller_id' => $this->id ])->get()->map(function($communeShippingMethod) {
            return $communeShippingMethod->getAvailableShippingMethodCodes();
        })->flatten()->unique();
        $finalMethods = ShippingMethod::whereIn('code', $availableShippingMethods)->where('status', 1)->get();
        
        return $finalMethods;
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller_category()
    {
        return $this->belongsTo(SellerCategory::class);
    }

    public function business_activity()
    {
        return $this->belongsTo(BusinessActivity::class);
    }

    public function addresses()
    {
        return $this->hasMany(SellerAddress::class);
    }

    public function plan_subscription()
    {
        return $this->hasMany(PlanSubscription::class);
    }

    public function shippingmethods()
    {
        return $this->belongsToMany(ShippingMethod::class,'shipping_method_seller_mapping');
    }

    public function shipping_method_seller()
    {
        return $this->hasMany(ShippingMethodSeller::class);
    }

    public function paymentmethods()
    {
        return $this->hasMany(PaymentMethodSeller::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(PlanSubscription::class,'plan_subscription_seller_mapping','user_id');
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

    public function getStatusDescriptionAttribute()
    {
        switch ($this->status) {
            case $this::STATUS_ACTIVE:
                return 'Activa';
                break;
            case $this::STATUS_INACTIVE:
                return 'Inactiva';
                break;
            default:
                break;
        }
    }

    public function getUidAttribute()
    {
        $rutFormatter = Rut::parse($this->attributes['uid']);

        return $rutFormatter->format();
    }

    public function getApprovedDescriptionAttribute()
    {
        switch ($this->is_approved) {
            case $this::REVIEW_STATUS_APPROVED:
                return 'Aprobado';
                break;
            case $this::REVIEW_STATUS_REJECTED:
                return 'Rechazado';
                break;
            default:
                return 'Pendiente'; //REVIEW_STATUS_PENDING
                break;
        }
    }

    public function getTransbankCodeAttribute()
    {
        if (!empty($this->payments_data)) {
            $payment = json_decode($this->payments_data);

            if(!empty($payment[0]->key)) {
                return $payment[0]->key;
            }
        }

        return '';
    }

    public function getCreatedAtFormatAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */

    public function setPasswordAttribute($value)
    {
        if ($value && $value != "") {
            $this->attributes['password'] = Hash::make(
                $value
            );
        }
    }

    public function setLogoAttribute($value)
    {
        $attribute_name = 'logo';

        $disk = 'public';

        // if the image was erased
        if ($value==null) {
            // delete the image from disk
            $deleteFile = Str::replaceFirst('/storage/','',$this->{$attribute_name});
            \Storage::disk($disk)->delete($deleteFile);

            // set null in the database column
            $this->attributes[$attribute_name] = null;
        }

        // if a base64 was sent, store it in the db
        if (Str::startsWith($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value.time()).'.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put('logos/'.$filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            $deleteFile = Str::replaceFirst('/storage/','',$this->{$attribute_name});
            \Storage::disk($disk)->delete($deleteFile);

            $this->attributes[$attribute_name] = '/storage/logos/' . $filename;
        }
    }

    public function setBannerAttribute($value)
    {
        $attribute_name = 'banner';

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
        if (Str::startsWith($value, 'data:image')) {
            // 0. Make the image
            $image = \Image::make($value)->encode('jpg', 90);

            // 1. Generate a filename.
            $filename = md5($value . time()) . '.jpg';

            // 2. Store the image on disk.
            \Storage::disk($disk)->put('logos/' . $filename, $image->stream());

            // 3. Delete the previous image, if there was one.
            $deleteFile = Str::replaceFirst('/storage/', '', $this->{$attribute_name});
            \Storage::disk($disk)->delete($deleteFile);

            $this->attributes[$attribute_name] = '/storage/logos/'.$filename;
        }
    }

    public function setUidAttribute($value)
    {
        $this->attributes['uid'] = str_replace('.', '', $value);
    }
}
