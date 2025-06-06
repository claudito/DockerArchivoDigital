<?php

namespace App\Models\ADIG;

use Illuminate\Database\Eloquent\Model;

class ADIG_ArchivoDigital extends Model
{
    //FIX
    protected $table = 'ADIG_ArchivoDigital';
    protected $connection = 'sqlsrv';

    protected $primaryKey = 'ID_ArchivoDigital';

    protected $guarded = [];
    public $timestamps = false;
}
