<?php
/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contact;
use Faker\Generator as Faker;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'uuid'      => $faker->uuid,
        'team_id'   => 1,
        'name'      => $faker->name,
        'phone'     => $faker->phoneNumber,
        'email'     => $faker->email,
        'meta_data' => '[]',
    ];
});
