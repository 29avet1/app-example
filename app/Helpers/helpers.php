<?php

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

require_once('db_cache_helpers.php');

/**
 * @param        $routes
 * @param string $output
 * @return string
 */
function active_route($routes, string $output = 'active')
{
    if (is_array($routes)) {
        foreach ($routes as $route) {
            if (Route::currentRouteName() == $route) {
                return $output;
            }
        }
    } else {
        if (strpos(Route::currentRouteName(), $routes) === 0) {
            return $output;
        }
    }

    return '';
}

/**
 * @param string $number
 * @param string $prefix
 * @param int    $defaultLength
 * @return string
 */
function num_prefix(string $number, string $prefix, int $defaultLength = 1): string
{
    $length = strlen($number);
    $prefixLength = ($length < $defaultLength) ? $defaultLength : $length + 1;

    return str_pad($number, $prefixLength, $prefix, STR_PAD_LEFT);
}

/**
 * @param $str
 * @return array
 */
function html_meta_tags($str)
{
    $pattern = '
  ~<\s*meta\s

  # using lookahead to capture type to $1
    (?=[^>]*?
    \b(?:name|property|http-equiv)\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  )

  # capture content to $2
  [^>]*?\bcontent\s*=\s*
    (?|"\s*([^"]*?)\s*"|\'\s*([^\']*?)\s*\'|
    ([^"\'>]*?)(?=\s*/?\s*>|\s\w+\s*=))
  [^>]*>

  ~ix';

    if (preg_match_all($pattern, $str, $out)) {
        return array_combine($out[1], $out[2]);
    }

    return [];
}

/**
 * @param string $string
 * @param string $character
 * @param bool   $firstItem
 * @return string
 */
function string_after(string $string, string $character, bool $firstItem = true): string
{
    $position = $firstItem ? strpos($string, $character) : strrpos($string, $character);

    return $position ? substr($string, $position + 1) : $string;
}

/**
 * @param string $string
 * @return string
 */
function string_camelcase_spaces(string $string): string
{
    return trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $string));
}

/**
 * @param array  $tagValues
 * @param string $string
 * @return mixed|string
 */
function string_replace_tags(array $tagValues, string $string): string
{
    foreach ($tagValues as $tag => $value) {
        $string = str_replace($tag, $value, $string);
    }

    return $string;
}

/**
 * @param string $string
 * @return array
 */
function extract_tags_from_text(string $string): array
{
    preg_match_all('/\{\{(.+?)\}\}/', $string, $output);

    return array_map('str_slug', array_values($output[1]));
}

/**
 * @param array $array
 * @param array $values
 * @return array
 */
function array_replace_values(array $array, array $values): array
{
    foreach ($values as $value => $replacement) {
        $array = array_replace($array,
            array_fill_keys(
                array_keys($array, $value),
                $replacement
            )
        );
    }

    return $array;
}

/**
 * @param array $array
 * @param array $keys
 * @return array
 */
function array_unset(array $array, array $keys): array
{
    foreach ($keys as $key) {
        unset($array[$key]);
    }

    return $array;
}

/**
 * @param array $array
 * @param array $keys
 * @return array
 */
function array_leave_only(array $array, array $keys): array
{
    foreach ($array as $key => $value) {
        if (!in_array($key, $keys)) {
            unset($array[$key]);
        }
    }

    return $array;
}

/**
 * @param string $mime
 * @return string
 */
function format_from_mime(string $mime): string
{
    $types = explode('/', explode(';', $mime)[0]);

    switch ($types[0]) {
        case 'image':
            return 'image';
        case 'video':
            return 'video';
        case 'audio':
            return 'audio';
        case 'text':
            return 'text';
        case 'application':
            switch ($types[1]) {
                case 'pdf':
                    return 'pdf';
                case 'vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                case 'vnd.ms-excel':
                case 'vnd.openxmlformats-officedocument.spreadsheetml.template':
                case 'vnd.ms-excel.sheet.macroEnabled.12':
                case 'vnd.ms-excel.template.macroEnabled.12':
                case 'vnd.ms-excel.addin.macroEnabled.12':
                case 'vnd.ms-excel.sheet.binary.macroEnabled.12':
                    return 'excel';
                case 'msword':
                case 'vnd.openxmlformats-officedocument.wordprocessingml.document':
                case 'vnd.openxmlformats-officedocument.wordprocessingml.template':
                case 'vnd.ms-word.document.macroEnabled.12':
                case 'vnd.ms-word.template.macroEnabled.12':
                    return 'word';
                case 'vnd.ms-powerpoint':
                case 'vnd.openxmlformats-officedocument.presentationml.presentation':
                case 'vnd.openxmlformats-officedocument.presentationml.template':
                case 'vnd.openxmlformats-officedocument.presentationml.slideshow':
                case 'vnd.ms-powerpoint.addin.macroEnabled.12':
                case 'vnd.ms-powerpoint.presentation.macroEnabled.12':
                case 'vnd.ms-powerpoint.template.macroEnabled.12':
                case 'vnd.ms-powerpoint.slideshow.macroEnabled.12':
                    return 'powerpoint';
                default:
                    return 'file';
            }
        default:
            return 'file';
    }
}

/**
 * @param bool $string
 * @return array|string
 */
function acceptable_mimes(bool $string = false)
{
    $mimes = [
        'text/plain',
        'text/csv',
        'text/plain',
        'text/xml',

        'application/json',
        'application/xml',
        'application/x-shockwave-flash',

        // images
        'image/png',
        'image/jpeg',
        'image/gif',
        'image/bmp',
        'image/vnd.microsoft.icon',
        'image/tiff',
        'image/tiff',
        'image/svg+xml',

        // archives
        'application/zip',
        'application/x-rar-compressed',
        'application/x-msdownload',
        'application/vnd.ms-cab-compressed',

        // audio/video
        'audio/*',
        'video/*',

        // adobe
        'application/pdf',
        'image/vnd.adobe.photoshop',
        'application/postscript',

        // ms office
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',

        'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'application/vnd.ms-word.document.macroEnabled.12',
        'application/vnd.ms-word.template.macroEnabled.12',

        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'application/vnd.ms-excel.sheet.macroEnabled.12',
        'application/vnd.ms-excel.template.macroEnabled.12',
        'application/vnd.ms-excel.addin.macroEnabled.12',
        'application/vnd.ms-excel.sheet.binary.macroEnabled.12',

        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'application/vnd.openxmlformats-officedocument.presentationml.template',
        'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'application/vnd.ms-powerpoint.addin.macroEnabled.12',
        'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
        'application/vnd.ms-powerpoint.template.macroEnabled.12',
        'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',

        'application/rtf',

        // open office
        'application/vnd.oasis.opendocument.text',
        'application/vnd.oasis.opendocument.spreadsheet',
    ];

    if ($string) {
        return implode(',', $mimes);
    }

    return $mimes;
}

/**
 * @param array       $dateIntervals
 * @param Carbon|null $endDay
 * @return array
 */
function end_dates(array $dateIntervals, Carbon $endDay = null): array
{
    $datePairs = [];
    foreach ($dateIntervals as $id => $date) {
        $nextId = $id + 1;
        $datePairs[] = [
            'start' => $date,
            'end'   => isset($dateIntervals[$nextId]) ?
                $dateIntervals[$nextId]->copy()->addDay(-1)->endOfDay() :
                ($endDay ?? $date->copy()->endOfDay())
        ];
    }

    return $datePairs;
}

/**
 * @param array $dates
 * @return array
 */
function dates_to_timestamp(array $dates): array
{
    return array_map(function ($date) {
        return $date->timestamp;
    }, $dates);
}

/**
 * @param string $uri
 * @return array
 */
function get_uri_params(string $uri): array
{
    preg_match_all('/\{(.+?)[\?\}]/', $uri, $output);

    return array_values($output[1]);
}

/**
 * @param int $dayNumber
 * @return string
 */
function day_name(int $dayNumber): string
{
    if (($dayNumber < 1) || ($dayNumber > 7)) {
        return '';
    }

    return [
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
        7 => 'sunday',
    ][$dayNumber];
}

/**
 * @param string $day
 * @return int
 */
function day_number(string $day): int
{
    $day = strtolower($day);

    return [
        'monday'    => 1,
        'tuesday'   => 2,
        'wednesday' => 3,
        'thursday'  => 4,
        'friday'    => 5,
        'saturday'  => 6,
        'sunday'    => 7,
    ][$day];
}

/**
 * @param bool $typed
 * @return array
 */
function webhook_types(bool $typed = false): array
{
    $webhookTypes = config('webhook-types');

    return $typed ? $webhookTypes : Arr::flatten($webhookTypes);
}