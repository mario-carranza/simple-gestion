<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class ProductReservation extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'product_reservations';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    // protected $fillable = [];
    // protected $hidden = [];
    protected $dates = [
        'check_in_date',
        'check_out_date',
        'created_at',
    ];

    const PENDING_STATUS = 'pending';
    const REJECTED_STATUS = 'rejected';
    const ACCEPTED_STATUS = 'accepted';
    const PAYED_STATUS = 'payed';
    const CANCELED_STATUS = 'canceled';

    const STATUS_DICTIRONARY = [
        self::PENDING_STATUS => 'Pendiente',
        self::REJECTED_STATUS => 'Rechazada',
        self::ACCEPTED_STATUS => 'Aceptada',
        self::PAYED_STATUS => 'Pagada',
        self::CANCELED_STATUS => 'Cancelada',
    ];
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
    public function product()
    {
        return $this->belongsTo(Product::class);
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
    public function getReservationStatusTextAttribute()
    {
        return self::STATUS_DICTIRONARY[$this->reservation_status] ?? 'Desconocido';
    }

    public function getCheckInDateOnlyDateAttribute()
    {
        return Carbon::parse($this->check_in_date)->format('Y-m-d');
    }

    public function getCheckOutDateOnlyDateAttribute()
    {
        return Carbon::parse($this->check_out_date)->format('Y-m-d');
    }

    public function getTypeTextAttribute()
    {
        switch ($this->type) {
            case 'housing':
                return 'Alojamiento';
                break;

            case 'tour':
                return 'Tour';
                break;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
