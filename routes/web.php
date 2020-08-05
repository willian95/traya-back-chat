<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('test', function () {
    event(new App\Events\HiringApplicant('Someone',16));
    return "Event has been sent!";
});

Route::get('/email', function () {
  return view('layouts.emails.templateComments');

});


Route::get('/', function () {
  return view('welcome');
  // return redirect('/login');
});

Route::get('/terminos-condiciones', function () {
  return view('terms');
  // return redirect('/login');
});

Route::get('/users/statistics', function(){

  $users = App\User::select("locations.name as location_name", "users.email", "users.name", "profiles.phone", "users.id", "profiles.domicile", "users.created_at")->join('profiles', "profiles.user_id", '=', "users.id")->join('locations', 'profiles.location_id', '=', "locations.id")->orderBy('users.created_at', 'desc')->get();
  return view("users", ["users" => $users]);

});

Route::get('traya-backend/public/api/fcm/test', 'FCMController@sendNotification');

/*Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware'=>'auth'], function () {

  ////////////////////////////API ROUTES
  Route::prefix('api')->group(function () {

  });//Api prefix

});

Route::get('redirect/{driver}', 'Auth\LoginController@redirectToProvider')
    ->name('login.provider');
    // ->where('driver', implode('|', config('auth.socialite.drivers')));

    Route::get('{driver}/callback', 'Auth\LoginController@handleProviderCallback')
    ->name('login.callback');*/
    //->where('driver', implode('|', config('auth.socialite.drivers')));



Route::post('traya-backend/public/api/users/statistics/count', 'Api\StatisticsController@UsersTypeCount');
Route::post('traya-backend/public/api/users/activeUsersLocation', 'Api\StatisticsController@UsersByLocation');
Route::post('traya-backend/public/api/enableAdministrator', 'Api\AdministratorController@enableAdministrator');
Route::post('traya-backend/public/api/disableAdministrator', 'Api\AdministratorController@disableAdministrator');
Route::post('traya-backend/public/api/users/statistics/send/report', 'Api\StatisticsController@sendReport');
//login with google
Route::post('traya-backend/public/api/social/socialAuth', 'Api\SocialAuthController@socialAuth');

//administradores por localidades
Route::post('traya-backend/public/api/administratorsLocation', 'Api\AdministratorController@administratorsByLocation');

Route::post('traya-backend/public/api/updateapk', 'Api\AuthController@updateApk'); //ruta para checkear si el apk es actual
Route::post('traya-backend/public/api/signup', 'Api\AuthController@register');
Route::post('traya-backend/public/api/login', 'Api\AuthController@login');
Route::post('traya-backend/public/api/fcm/token/update', 'Api\AuthController@updateFCMToken');
Route::post('traya-backend/public/api/recoveryPassword', 'Api\AuthController@recoveryPassword');
Route::post('traya-backend/public/api/contact', 'Api\AuthController@contact');
Route::middleware('jwt.refresh')->get('traya-backend/public/api/token/refresh', 'Api\AuthController@refresh');
Route::group(['prefix' => 'traya-backend/public/api/auth', 'middleware' => 'jwt.auth'], function () {
  //Retorna datos del usuario autenticado
  Route::get('user', 'Api\AuthController@user');
  //Actualizar datos de usuario / perfil
  Route::post('user/update', 'Api\AuthController@update');

  //Cerrar sesión
  Route::post('logout', 'Api\AuthController@logout');

  //

});
//Obtener datos perfil de X usuario
Route::get('traya-backend/public/api/user/{id}', 'Api\AuthController@dataUser');
//Obtener servicios
Route::get('traya-backend/public/api/services', 'Api\ServicesController@index');
//Obtener usuarios asociados a un servicio
Route::get('traya-backend/public/api/services_user', 'Api\ServicesUserController@users');
//Obtener contrataciones
Route::get('traya-backend/public/api/hiring', 'Api\HiringsController@index');
//Ver resumen de una contratación
Route::get('traya-backend/public/api/hiring/{id}', 'Api\HiringsController@show');
//Ver notificaciones de un usuario
Route::get('traya-backend/public/api/notification/{id}/is_worker/{worker}', 'Api\NotificationController@getNotifications');

//Obtener localidades
Route::get('traya-backend/public/api/locations', 'Api\LocationController@index');
Route::post('traya-backend/public/api/userLastAction', 'Api\AuthController@storeLastAction');

//contar cantidad de solicitudes
Route::post('traya-backend/public/api/countActive/hiring', 'Api\HiringsController@countActiveHiring');

Route::group(['middleware'=>'jwt.auth'], function () {

  Route::post('traya-backend/public/api/changeAddress', 'Api\AuthController@changeAddress');

  Route::get('traya-backend/public/api/users', 'Api\AuthController@index');
  Route::delete('traya-backend/public/api/users/{id}', 'Api\AuthController@delete');
  Route::get('traya-backend/public/api/users/{id}/restore', 'Api\AuthController@restore');

  //Crear contratacion
  Route::post('traya-backend/public/api/hiring', 'Api\HiringsController@store');
  //Crear contratacion
  Route::post('traya-backend/public/api/hiring_mt4', 'Api\HiringsController@mt4');
  //Actualizar estado contratacion
  Route::put('traya-backend/public/api/hiring', 'Api\HiringsController@update');
  Route::post('traya-backend/public/api/hiring/contact', 'Api\HiringsController@storeContact');

  //Actualizar estado de notificación
  Route::put('traya-backend/public/api/notification/{id}', 'Api\NotificationController@markRead');
  //Actualizar mostrar mapa
  Route::post('traya-backend/public/api/hiring/update/map/{hiring_id}', 'Api\HiringsController@updateShowMap');
  //Obtiene mapas
  Route::get('traya-backend/public/api/hiring/get/map/{hiring_id}', 'Api\HiringsController@getMaps');
  //Obtiene posiciónde usuarios
  Route::post('traya-backend/public/api/hiring/get/position', 'Api\HiringsController@getUserPosition');

  //Crear servicio
  Route::post('traya-backend/public/api/services', 'Api\ServicesController@store');
  //Actualizar servicio
  Route::put('traya-backend/public/api/services', 'Api\ServicesController@update');
  //Borrar servicio
  Route::delete('traya-backend/public/api/services/{id}', 'Api\ServicesController@delete');
  //Asociar servicio a usuario autenticado
  Route::post('traya-backend/public/api/services_user', 'Api\ServicesUserController@store');

  //Config application
  Route::get('traya-backend/public/api/config', 'Api\ConfigController@index');
  Route::post('traya-backend/public/api/config', 'Api\ConfigController@store');


  //Crear localidad
  Route::post('traya-backend/public/api/locations', 'Api\LocationController@store');
  //buscar localidad
  Route::get('traya-backend/public/api/location/{id}', 'Api\LocationController@find');
  //Actualizar una localidad
  Route::put('traya-backend/public/api/locations/{id}', 'Api\LocationController@update');
  //Eliminar una localidad
  Route::delete('traya-backend/public/api/locations/{id}', 'Api\LocationController@delete');

});//middle auth

//Publicidad
Route::post('traya-backend/public/api/ads', 'Api\AdsController@index');

//Administrador de publicidad
Route::post('traya-backend/public/api/administrator/ad/store', 'Api\AdministratorController@storeAd');
Route::get('traya-backend/public/api/administrator/ad/getType', 'Api\AdministratorController@getAdTypes');
Route::get('traya-backend/public/api/administrator/ad/location/{location_id}', 'Api\AdministratorController@getAds');
Route::delete('traya-backend/public/api/administrator/ad/delete/{id}', 'Api\AdministratorController@deleteAd');

