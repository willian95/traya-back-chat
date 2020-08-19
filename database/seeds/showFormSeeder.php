<?php

use Illuminate\Database\Seeder;
use App\ShowForm;

class showFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $showForm = new ShowForm;
        $showForm->show = false;
        $showForm->save();
    }
}
