<?php
namespace App\Models;

class ServerExtension extends Extension
{
    public function newQuery($excludeDeleted = true)
    {
        return parent::newQuery($excludeDeleted)->where('type', 'server');
    }
}
