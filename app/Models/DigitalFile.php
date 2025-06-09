<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DigitalFile extends Model
{
    //
    protected $fillable = [
        'name',
        'content_type',
        'size_bytes',
        'storage_path',
        'hash',
        'user_id'

    ];
}
