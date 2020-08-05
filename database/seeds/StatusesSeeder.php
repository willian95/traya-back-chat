<?php

use Illuminate\Database\Seeder;
use App\Status;
class StatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $statuses=[
        [
          'id'=>1,
          'name'=>'En espera de ok',
        ],
        [
          'id'=>2,
          'name'=>'Trabajador disponible',
        ],
        [
          'id'=>3,
          'name'=>'Contratado',
        ],
        [
          'id'=>4,
          'name'=>'Completado',
        ],
        [
          'id'=>5,
          'name'=>'Cancelado',
        ],
      ];//Array roles
      $statuses=json_decode(json_encode($statuses));//Convierto en colección de datos
      foreach($statuses as $status){
        $p=Status::updateOrCreate(
          [
            'id'=>$status->id
          ],
          [
            'id'=>$status->id,
            'name'=>$status->name,
          ]
        );
      }//foreach
    }//run()
}
