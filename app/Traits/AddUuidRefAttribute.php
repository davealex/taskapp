<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait AddUuidRefAttribute
{
    /**
     * @return void
     */
    public static function bootAddUuidRefAttribute(): void
    {
        static::creating(function ($model) {
            $model->ref = Str::orderedUuid();
        });
    }
}
