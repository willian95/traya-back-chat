<?php

use Illuminate\Database\Seeder;
use App\Location;
use App\ClaimLocality;

class ClaimLocalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        if(ClaimLocality::count() <= 0){
            foreach(Location::all() as $location){

                $claim = new ClaimLocality;
                $claim->location_id = $location->id;
                $claim->save();

            }
        }

    }
}
