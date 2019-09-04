<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Country
 * @property integer $id
 * @property string  $name
 * @property string  $local_name
 * @property string  $code
 * @mixin \Eloquent
 * @package App
 */
class Country extends Model
{
    protected $table = 'countries';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'local_name',
        'phone',
        'language_code',
        'language_name',
        'local_language_name',
        'postal_code_required',
    ];

}