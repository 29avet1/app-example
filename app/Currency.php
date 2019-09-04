<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Currency
 * @property integer $id
 * @property string  $name
 * @property string  $code
 * @property string  $decimals
 * @mixin \Eloquent
 * @package App
 */
class Currency extends Model
{
    protected $table = 'currencies';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'decimals',
    ];
}