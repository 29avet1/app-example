<?php

namespace App\Traits\Models;

trait HasEnumValue
{
    /**
     * @param string|null $enum
     * @return array
     */
    public static function getEnum(string $enum = null)
    {
        if ($enum) {
            return array_get(self::$enum, $enum);
        }

        return self::$enum;
    }

    /**
     * @param string $enum
     * @param string $value
     * @param bool   $throwError
     * @return bool
     */
    public static function checkEnum(string $enum, string $value, bool $throwError = true)
    {
        if (in_array($value, self::getEnum($enum))) {
            return true;
        }

        if ($throwError) {
            abort(500, "Wrong enum value for {$enum}.");
        }

        return false;
    }
}