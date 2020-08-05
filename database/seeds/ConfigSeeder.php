<?php

use Illuminate\Database\Seeder;
use App\Config;
class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $p=Config::updateOrCreate(
        [
          'id'=>1
        ],
        [
          'active'=>true,
        ]
      );
    }
}
