<?php

namespace App\Traits;

trait UseRefAsRouteKeyNameTrait
{
    /**
     * get route-model binding attribute.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'ref';
    }
}
