<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Media as BaseMedia;

class Media extends BaseMedia
{
    protected $table = 'media';

    protected $appends = [
        'full_url'
    ];

    public function getFullUrlAttribute()
    {
        return $this->getUrl();
    }
}
