<?php
namespace App\Models;

class Gateway extends Extension
{
    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)->where('type', 'gateway');
    }
}
