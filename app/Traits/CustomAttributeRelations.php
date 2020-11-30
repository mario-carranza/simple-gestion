<?php

namespace App\Traits;

use App\Models\AttributeValue;

trait CustomAttributeRelations
{
    public function attribute_values()
    {
        return $this->morphMany(AttributeValue::class, 'model');
    }
}
