<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = [
            // Us States
            [
                'name'         => 'Alabama',
                'code'         => 'AL',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Alaska',
                'code'         => 'AK',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Arizona',
                'code'         => 'AZ',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Arkansas',
                'code'         => 'AR',
                'country_code' => 'US',
            ],
            [
                'name'         => 'California',
                'code'         => 'CA',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Colorado',
                'code'         => 'CO',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Connecticut',
                'code'         => 'CT',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Delaware',
                'code'         => 'DE',
                'country_code' => 'US',
            ],
            [
                'name'         => 'District of Columbia',
                'code'         => 'DC',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Florida',
                'code'         => 'FL',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Georgia',
                'code'         => 'GA',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Hawaii',
                'code'         => 'HI',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Idaho',
                'code'         => 'ID',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Illinois',
                'code'         => 'IL',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Indiana',
                'code'         => 'IN',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Iowa',
                'code'         => 'IA',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Kansas',
                'code'         => 'KS',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Kentucky',
                'code'         => 'KY',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Louisiana',
                'code'         => 'LA',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Maine',
                'code'         => 'ME',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Maryland',
                'code'         => 'MD',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Massachusetts',
                'code'         => 'MA',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Michigan',
                'code'         => 'MI',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Minnesota',
                'code'         => 'MN',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Mississippi',
                'code'         => 'MS',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Missouri',
                'code'         => 'MO',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Montana',
                'code'         => 'MT',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Nebraska',
                'code'         => 'NE',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Nevada',
                'code'         => 'NV',
                'country_code' => 'US',
            ],
            [
                'name'         => 'New Hampshire',
                'code'         => 'NH',
                'country_code' => 'US',
            ],
            [
                'name'         => 'New Jersey',
                'code'         => 'NJ',
                'country_code' => 'US',
            ],
            [
                'name'         => 'New Mexico',
                'code'         => 'NM',
                'country_code' => 'US',
            ],
            [
                'name'         => 'New York',
                'code'         => 'NY',
                'country_code' => 'US',
            ],
            [
                'name'         => 'North Carolina',
                'code'         => 'NC',
                'country_code' => 'US',
            ],
            [
                'name'         => 'North Dakota',
                'code'         => 'ND',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Ohio',
                'code'         => 'OH',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Oklahoma',
                'code'         => 'OK',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Oregon',
                'code'         => 'OR',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Pennsylvania',
                'code'         => 'PA',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Rhode Island',
                'code'         => 'RI',
                'country_code' => 'US',
            ],
            [
                'name'         => 'South Carolina',
                'code'         => 'SC',
                'country_code' => 'US',
            ],
            [
                'name'         => 'South Dakota',
                'code'         => 'SD',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Tennessee',
                'code'         => 'TN',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Texas',
                'code'         => 'TX',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Utah',
                'code'         => 'UT',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Vermont',
                'code'         => 'VT',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Virginia',
                'code'         => 'VA',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Washington',
                'code'         => 'WA',
                'country_code' => 'US',
            ],
            [
                'name'         => 'West Virginia',
                'code'         => 'WV',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Wisconsin',
                'code'         => 'WI',
                'country_code' => 'US',
            ],
            [
                'name'         => 'Wyoming',
                'code'         => 'WY',
                'country_code' => 'US',
            ],
            // Canada provinces
            [
                'name'         => 'Alberta',
                'code'         => 'AB',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'British Columbia',
                'code'         => 'BC',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'Labrador',
                'code'         => 'LB',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'Manitoba',
                'code'         => 'MB',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'New Brunswick',
                'code'         => 'NB',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'Newfoundland',
                'code'         => 'NF',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'Nova Scotia',
                'code'         => 'NS',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'Nunavut',
                'code'         => 'NU',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'North West Terr.',
                'code'         => 'NW',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'Ontario',
                'code'         => 'ON',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'Prince Edward Is.',
                'code'         => 'PE',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'Quebec',
                'code'         => 'QC',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'Saskatchewen',
                'code'         => 'SK',
                'country_code' => 'CA',
            ],
            [
                'name'         => 'Yukon',
                'code'         => 'YU',
                'country_code' => 'CA',
            ],
        ];

        DB::table('states')->insert($states);
    }
}
