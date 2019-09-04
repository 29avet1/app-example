<?php

namespace App\Traits\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

trait UsesUuids
{
//    /**
//     * Get the route key for the model.
//     *
//     * @return string
//     */
//    public function getRouteKeyName()
//    {
//        return 'uuid';
//    }

    /**
     * @param string $uid
     * @param bool   $fail
     * @return null|Model
     * @throws Exception
     */
    public static function findByUid($uid, $fail = true)
    {
        if (!Uuid::isValid($uid)) {
            abort(400, 'The given id is not valid');
        }

        $query = self::where('uuid', $uid);

        return $fail ? $query->firstOrFail() : $query->first();
    }
}