<?php

use Illuminate\Database\Seeder;
use App\AdType;

class AdTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adType = new AdType;
        $adType->id = 1;
        $adType->name = "base";
        $adType->save();

        $adType = new AdType;
        $adType->id = 2;
        $adType->name = "superior";
        $adType->save();

        $adType = new AdType;
        $adType->id = 3;
        $adType->name = "premium";
        $adType->save();
    }
}
