<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = [
            [
                'code'     => 'AED',
                'name'     => 'United Arab Emirates dirham',
                'decimals' => 2,
            ],
            [
                'code'     => 'AFN',
                'name'     => 'Afghanistan afghani',
                'decimals' => 2,
            ],
            [
                'code'     => 'AMD',
                'name'     => 'Armenian dram',
                'decimals' => 2,
            ],
            [
                'code'     => 'ANG',
                'name'     => 'Netherlands Antillean guilder',
                'decimals' => 2,
            ],
            [
                'code'     => 'AOA',
                'name'     => 'Angola kwanza',
                'decimals' => 2,
            ],
            [
                'code'     => 'ARS',
                'name'     => 'Argentine peso',
                'decimals' => 2,
            ],
            [
                'code'     => 'AUD',
                'name'     => 'Australian dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'AWG',
                'name'     => 'Aruban guilder',
                'decimals' => 2,
            ],
            [
                'code'     => 'AZN',
                'name'     => 'Azerbaijanian manat',
                'decimals' => 2,
            ],
            [
                'code'     => 'BAM',
                'name'     => 'Bosnia and Herzegovina convertible mark',
                'decimals' => 2,
            ],
            [
                'code'     => 'BBD',
                'name'     => 'Barbados dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'BDT',
                'name'     => 'Bangladeshi taka',
                'decimals' => 2,
            ],
            [
                'code'     => 'BGN',
                'name'     => 'Bulgarian lev',
                'decimals' => 2,
            ],
            [
                'code'     => 'BHD',
                'name'     => 'Bahraini dinar',
                'decimals' => 3,
            ],
            [
                'code'     => 'BIF',
                'name'     => 'Burundian franc',
                'decimals' => 0,
            ],
            [
                'code'     => 'BMD',
                'name'     => 'Bermuda dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'BND',
                'name'     => 'Brunei dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'BOB',
                'name'     => 'Bolivian boliviano',
                'decimals' => 2,
            ],
            [
                'code'     => 'BRL',
                'name'     => 'Brazilian real',
                'decimals' => 2,
            ],
            [
                'code'     => 'BSD',
                'name'     => 'Bahamian dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'BWP',
                'name'     => 'Botswana pula',
                'decimals' => 2,
            ],
            [
                'code'     => 'BYN',
                'name'     => 'Belarussian ruble',
                'decimals' => 2,
            ],
            [
                'code'     => 'BZD',
                'name'     => 'Belize dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'CAD',
                'name'     => 'Canadian dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'CDF',
                'name'     => 'Congolese franc',
                'decimals' => 2,
            ],
            [
                'code'     => 'CHF',
                'name'     => 'Swiss franc',
                'decimals' => 2,
            ],
            [
                'code'     => 'CLP',
                'name'     => 'Chilean peso',
                'decimals' => 0,
            ],
            [
                'code'     => 'CNY',
                'name'     => 'Chinese yuan renminbi',
                'decimals' => 2,
            ],
            [
                'code'     => 'COP',
                'name'     => 'Columbian peso',
                'decimals' => 2,
            ],
            [
                'code'     => 'CRC',
                'name'     => 'Costa Rican colon',
                'decimals' => 2,
            ],
            [
                'code'     => 'CVE',
                'name'     => 'Cape Verde escudo',
                'decimals' => 2,
            ],
            [
                'code'     => 'CZK',
                'name'     => 'Czech koruna',
                'decimals' => 2,
            ],
            [
                'code'     => 'DJF',
                'name'     => 'Djiboutian franc',
                'decimals' => 0,
            ],
            [
                'code'     => 'DKK',
                'name'     => 'Danish krone',
                'decimals' => 2,
            ],
            [
                'code'     => 'DOP',
                'name'     => 'Dominican peso',
                'decimals' => 2,
            ],
            [
                'code'     => 'DZD',
                'name'     => 'Algerian dinar',
                'decimals' => 2,
            ],
            [
                'code'     => 'EGP',
                'name'     => 'Egyptian pound',
                'decimals' => 2,
            ],
            [
                'code'     => 'ERN',
                'name'     => 'Eritrean nakfa',
                'decimals' => 2,
            ],
            [
                'code'     => 'ETB',
                'name'     => 'Ethiopian birr',
                'decimals' => 2,
            ],
            [
                'code'     => 'EUR',
                'name'     => 'Euro',
                'decimals' => 2,
            ],
            [
                'code'     => 'FJD',
                'name'     => 'Fiji dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'FKP',
                'name'     => 'Falkland Islands pound',
                'decimals' => 2,
            ],
            [
                'code'     => 'GBP',
                'name'     => 'British pound sterling',
                'decimals' => 2,
            ],
            [
                'code'     => 'GEL',
                'name'     => 'Georgian lari',
                'decimals' => 2,
            ],
            [
                'code'     => 'GHS',
                'name'     => 'Ghana cedi',
                'decimals' => 2,
            ],
            [
                'code'     => 'GIP',
                'name'     => 'Gibraltar pound',
                'decimals' => 2,
            ],
            [
                'code'     => 'GMD',
                'name'     => 'Gambian dalasi',
                'decimals' => 2,
            ],
            [
                'code'     => 'GNF',
                'name'     => 'Guinean franc',
                'decimals' => 0,
            ],
            [
                'code'     => 'GTQ',
                'name'     => 'Guatemalan quetzal',
                'decimals' => 2,
            ],
            [
                'code'     => 'GYD',
                'name'     => 'Guyanese dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'HKD',
                'name'     => 'Hong Kong dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'HNL',
                'name'     => 'Hunduran Lempira',
                'decimals' => 2,
            ],
            [
                'code'     => 'HRK',
                'name'     => 'Croatian kuna',
                'decimals' => 2,
            ],
            [
                'code'     => 'HTG',
                'name'     => 'Haitian gourde',
                'decimals' => 2,
            ],
            [
                'code'     => 'HUF',
                'name'     => 'Hungarian forint',
                'decimals' => 2,
            ],
            [
                'code'     => 'IDR',
                'name'     => 'Indonesian rupiah',
                'decimals' => 2,
            ],
            [
                'code'     => 'ILS',
                'name'     => 'Israeli sheqel',
                'decimals' => 0,
            ],
            [
                'code'     => 'INR',
                'name'     => 'Indian rupee',
                'decimals' => 2,
            ],
            [
                'code'     => 'IQD',
                'name'     => 'Iraqi dinar',
                'decimals' => 3,
            ],
            [
                'code'     => 'ISK',
                'name'     => 'Icelandic krona',
                'decimals' => 2,
            ],
            [
                'code'     => 'JMD',
                'name'     => 'Jamaican dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'JOD',
                'name'     => 'Jordanian dinar',
                'decimals' => 3,
            ],
            [
                'code'     => 'JPY',
                'name'     => 'Japanese yen',
                'decimals' => 0,
            ],
            [
                'code'     => 'KES',
                'name'     => 'Kenyan shilling',
                'decimals' => 2,
            ],
            [
                'code'     => 'KGS',
                'name'     => 'Kyrgyzstani som',
                'decimals' => 2,
            ],
            [
                'code'     => 'KHR',
                'name'     => 'Cambodian riel',
                'decimals' => 2,
            ],
            [
                'code'     => 'KMF',
                'name'     => 'Comoro franc',
                'decimals' => 0,
            ],
            [
                'code'     => 'KRW',
                'name'     => 'South Korean won',
                'decimals' => 0,
            ],
            [
                'code'     => 'KWD',
                'name'     => 'Kuwaiti dinar',
                'decimals' => 3,
            ],
            [
                'code'     => 'KYD',
                'name'     => 'Cayman Islands dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'KZT',
                'name'     => 'Kazakhstani tenge',
                'decimals' => 2,
            ],
            [
                'code'     => 'LAK',
                'name'     => 'Lao kip',
                'decimals' => 2,
            ],
            [
                'code'     => 'LBP',
                'name'     => 'Lebanese pound',
                'decimals' => 2,
            ],
            [
                'code'     => 'LKR',
                'name'     => 'Sri Lanka rupee',
                'decimals' => 2,
            ],
            [
                'code'     => 'LRD',
                'name'     => 'Liberian dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'LTL',
                'name'     => 'Lithuanian litas',
                'decimals' => 2,
            ],
            [
                'code'     => 'LVL',
                'name'     => 'Latvian lats',
                'decimals' => 2,
            ],
            [
                'code'     => 'MAD',
                'name'     => 'Moroccan dirham',
                'decimals' => 2,
            ],
            [
                'code'     => 'MDL',
                'name'     => 'Moldovan leu',
                'decimals' => 2,
            ],
            [
                'code'     => 'MGA',
                'name'     => 'Malagasy ariary',
                'decimals' => 0,
            ],
            [
                'code'     => 'MKD',
                'name'     => 'Macedonian denar',
                'decimals' => 2,
            ],
            [
                'code'     => 'MMK',
                'name'     => 'Myanmar kyat',
                'decimals' => 2,
            ],
            [
                'code'     => 'MNT',
                'name'     => 'Mongolian tugrik',
                'decimals' => 2,
            ],
            [
                'code'     => 'MOP',
                'name'     => 'Macanese pataca',
                'decimals' => 2,
            ],
            [
                'code'     => 'MRO',
                'name'     => 'Mauritanian ouguiya',
                'decimals' => 2,
            ],
            [
                'code'     => 'MUR',
                'name'     => 'Mauritius rupee',
                'decimals' => 2,
            ],
            [
                'code'     => 'MVR',
                'name'     => 'Maldivian rufiyaa',
                'decimals' => 2,
            ],
            [
                'code'     => 'MWK',
                'name'     => 'Malawian kwacha',
                'decimals' => 2,
            ],
            [
                'code'     => 'MXN',
                'name'     => 'Mexican peso',
                'decimals' => 2,
            ],
            [
                'code'     => 'MYR',
                'name'     => 'Malaysian ringgit',
                'decimals' => 2,
            ],
            [
                'code'     => 'MZN',
                'name'     => 'Mozambican metical',
                'decimals' => 2,
            ],
            [
                'code'     => 'NAD',
                'name'     => 'Namibian dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'NGN',
                'name'     => 'Nigerian naira',
                'decimals' => 2,
            ],
            [
                'code'     => 'NIO',
                'name'     => 'Cordoba oro',
                'decimals' => 2,
            ],
            [
                'code'     => 'NOK',
                'name'     => 'Norwegian krone',
                'decimals' => 2,
            ],
            [
                'code'     => 'NPR',
                'name'     => 'Nepalese rupee',
                'decimals' => 2,
            ],
            [
                'code'     => 'NZD',
                'name'     => 'New Zealand dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'OMR',
                'name'     => 'Omani rial',
                'decimals' => 3,
            ],
            [
                'code'     => 'PAB',
                'name'     => 'Panamanian balboa',
                'decimals' => 2,
            ],
            [
                'code'     => 'PEN',
                'name'     => 'Peruvian nuevo sol',
                'decimals' => 2,
            ],
            [
                'code'     => 'PGK',
                'name'     => 'Papua New Guinean kina',
                'decimals' => 2,
            ],
            [
                'code'     => 'PHP',
                'name'     => 'Philippine peso',
                'decimals' => 2,
            ],
            [
                'code'     => 'PKR',
                'name'     => 'Pakistan rupee',
                'decimals' => 2,
            ],
            [
                'code'     => 'PLN',
                'name'     => 'Polish zloty',
                'decimals' => 2,
            ],
            [
                'code'     => 'PYG',
                'name'     => 'Paraguayan guarani',
                'decimals' => 0,
            ],
            [
                'code'     => 'QAR',
                'name'     => 'Qatari rial',
                'decimals' => 2,
            ],
            [
                'code'     => 'RON',
                'name'     => 'Romanian leu',
                'decimals' => 2,
            ],
            [
                'code'     => 'RSD',
                'name'     => 'Serbian dinar',
                'decimals' => 2,
            ],
            [
                'code'     => 'RUB',
                'name'     => 'Russian ruble',
                'decimals' => 2,
            ],
            [
                'code'     => 'RWF',
                'name'     => 'Rwanda franc',
                'decimals' => 0,
            ],
            [
                'code'     => 'SAR',
                'name'     => 'Saudi Arabian riyal',
                'decimals' => 2,
            ],
            [
                'code'     => 'SBD',
                'name'     => 'Solomon Islands dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'SCR',
                'name'     => 'Seychelles rupee',
                'decimals' => 2,
            ],
            [
                'code'     => 'SEK',
                'name'     => 'Swedish krona',
                'decimals' => 2,
            ],
            [
                'code'     => 'SGD',
                'name'     => 'Singapore dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'SHP',
                'name'     => 'Saint Helena pound',
                'decimals' => 2,
            ],
            [
                'code'     => 'SLL',
                'name'     => 'Sierra Leonean leone',
                'decimals' => 2,
            ],
            [
                'code'     => 'SOS',
                'name'     => 'Somali shilling',
                'decimals' => 2,
            ],
            [
                'code'     => 'SRD',
                'name'     => 'Surinamese dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'STD',
                'name'     => 'Sao Tome and Principe dobra',
                'decimals' => 2,
            ],
            [
                'code'     => 'SYP',
                'name'     => 'Syrian pound',
                'decimals' => 2,
            ],
            [
                'code'     => 'SZL',
                'name'     => 'Swaziland lilangeni',
                'decimals' => 2,
            ],
            [
                'code'     => 'THB',
                'name'     => 'Thai baht',
                'decimals' => 2,
            ],
            [
                'code'     => 'TJS',
                'name'     => 'Tajikistani somoni',
                'decimals' => 2,
            ],
            [
                'code'     => 'TND',
                'name'     => 'Tunisian dinar',
                'decimals' => 3,
            ],
            [
                'code'     => 'TOP',
                'name'     => 'Tongan pa’anga',
                'decimals' => 2,
            ],
            [
                'code'     => 'TRY',
                'name'     => 'Turkish lira',
                'decimals' => 2,
            ],
            [
                'code'     => 'TTD',
                'name'     => 'Trinidad and Tobago dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'TWD',
                'name'     => 'Taiwan dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'TZS',
                'name'     => 'Tanzanian shilling',
                'decimals' => 2,
            ],
            [
                'code'     => 'UAH',
                'name'     => 'Ukrainian hryvnia',
                'decimals' => 2,
            ],
            [
                'code'     => 'UGX',
                'name'     => 'Ugandan shilling',
                'decimals' => 0,
            ],
            [
                'code'     => 'USD',
                'name'     => 'United States dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'UYU',
                'name'     => 'Uruguayan peso',
                'decimals' => 2,
            ],
            [
                'code'     => 'UZS',
                'name'     => 'Uzbekistan som',
                'decimals' => 2,
            ],
            [
                'code'     => 'VEF',
                'name'     => 'Venezuelan bolivar fuerte',
                'decimals' => 2,
            ],
            [
                'code'     => 'VND',
                'name'     => 'Vietnamese dong',
                'decimals' => 0,
            ],
            [
                'code'     => 'VUV',
                'name'     => 'Vanuatu vatu',
                'decimals' => 0,
            ],
            [
                'code'     => 'WST',
                'name'     => 'Samoan tala',
                'decimals' => 2,
            ],
            [
                'code'     => 'XAF',
                'name'     => 'CFA franc BEAC (Central African CFA franc)',
                'decimals' => 0,
            ],
            [
                'code'     => 'XCD',
                'name'     => 'East Caribbean dollar',
                'decimals' => 2,
            ],
            [
                'code'     => 'XOF',
                'name'     => 'CFA Franc BCEAO (West African CFA franc)',
                'decimals' => 0,
            ],
            [
                'code'     => 'XPF',
                'name'     => 'CFP franc',
                'decimals' => 0,
            ],
            [
                'code'     => 'YER',
                'name'     => 'Yemeni rial',
                'decimals' => 2,
            ],
            [
                'code'     => 'ZAR',
                'name'     => 'South African rand',
                'decimals' => 2,
            ],
            [
                'code'     => 'ZMW',
                'name'     => 'Zambian kwacha',
                'decimals' => 2,
            ],
            [
                'code'     => 'ZWD',
                'name'     => 'Zimbabwean dollar',
                'decimals' => 2,
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
