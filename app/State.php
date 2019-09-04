<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Country
 * @property integer $id
 * @property string  $name
 * @property string  $code
 * @property string  $country_code
 * @mixin \Eloquent
 * @package App
 */
class State extends Model
{
    protected $table = 'states';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'country_code',
    ];
}