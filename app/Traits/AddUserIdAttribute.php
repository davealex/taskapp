<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait AddUserIdAttribute
{
    /**
     * @return void
     */
    public static function bootAddUserIdAttribute(): void
    {
        static::creating(function ($model) {
            $model->user_id = auth()->id();
        });
    }
}
