<?php

use Illuminate\Database\Seeder;
use Backpack\PermissionManager\app\Models\Role;
class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $roles=[
        [
          'id'=>1,
          'name'=>'Demandante',
          'guard_name'=>'backpack'
        ],
        [
          'id'=>2,
          'name'=>'Ofertante',
          'guard_name'=>'backpack'
        ]
      ];//Array roles
      $roles=json_decode(json_encode($roles));//Convierto en colección de datos
      foreach($roles as $rol){
        $p=Role::updateOrCreate(
          [
            'id'=>$rol->id
          ],
          [
            'id'=>$rol->id,
            'name'=>$rol->name,
            'guard_name'=>$rol->guard_name
          ]
        );
      }//foreach estados
    }//run()
}
