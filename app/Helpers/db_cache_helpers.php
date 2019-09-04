<?php

use App\Country;
use App\Currency;
use Illuminate\Support\Facades\Cache;

/**
 * @param string $currencyCode
 * @param bool   $fail
 * @return Currency
 * @throws Exception
 */
function currency(string $currencyCode, bool $fail = true): ?Currency
{
    $keyword = 'app.currencies';
    if (!Cache::has($keyword)) {
        Cache::forever($keyword, Currency::all());
    }

    $currencyCode = strtoupper($currencyCode);
    $currencies = Cache::get($keyword);
    $currency = $currencies->where('code', $currencyCode)->first();
    if ($fail && !$currency) {
        abort(404, "Currency {$currencyCode} not found.");
    }

    return $currency;
}


/**
 * @param string $countryCode
 * @param bool   $fail
 * @return Country
 * @throws Exception
 */
function country(string $countryCode, bool $fail = true): ?Country
{
    $keyword = 'app.countries';

    if (!Cache::has($keyword)) {
        Cache::forever($keyword, Country::all());
    }

    $countries = Cache::get($keyword);
    $country = $countries->where('code', $countryCode)->first();
    if ($fail && !$country) {
        abort(404, "Country {$countryCode} not found.");
    }

    return $country;
}
