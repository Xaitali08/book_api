<?php

namespace App\Models;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Image extends Model
{
    protected $fillable = ['imageable_id', 'imageable_type', 'path'];

    public function imageable()

    {
        return $this->morohTo();
    }
    public function url()
    {
        return URL::to('storage/' . $this->path);
    }

}
