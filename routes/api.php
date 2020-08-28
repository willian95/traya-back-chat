<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('fcm/test', 'FCMController@sendNotification');

Route::post('/users/statistics/count', 'Api\StatisticsController@UsersTypeCount');
Route::post('/users/activeUsersLocation', 'Api\StatisticsController@UsersByLocation');
Route::post('enableAdministrator', 'Api\AdministratorController@enableAdministrator');
Route::post('disableAdministrator', 'Api\AdministratorController@disableAdministrator');
Route::post('/users/statistics/send/report', 'Api\StatisticsController@sendReport');
//login with google
Route::post('/social/socialAuth', 'Api\SocialAuthController@socialAuth');

//administradores por localidades
Route::post('administratorsLocation', 'Api\AdministratorController@administratorsByLocation');

Route::post("user/update/image", 'Api\AuthController@updateImage');
Route::post("user/update/camera/{user_id}", 'Api\AuthController@updateCamera');

Route::get("/register/count", "Api\AuthController@showForm");

Route::post('updateapk', 'Api\AuthController@updateApk'); //ruta para checkear si el apk es actual
Route::post('signup', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');
Route::post('fcm/token/update', 'Api\AuthController@updateFCMToken');
Route::post('recoveryPassword', 'Api\AuthController@recoveryPassword');
Route::post('contact', 'Api\AuthController@contact');
Route::middleware('jwt.refresh')->get('/token/refresh', 'Api\AuthController@refresh');
Route::group(['prefix' => 'auth', 'middleware' => 'jwt.auth'], function () {
  //Retorna datos del usuario autenticado
  Route::get('user', 'Api\AuthController@user');
  //Actualizar datos de usuario / perfil
  Route::post('user/update', 'Api\AuthController@update');

  //Cerrar sesión
  Route::post('logout', 'Api\AuthController@logout');

  //

});
//Obtener datos perfil de X usuario
Route::get('user/{id}', 'Api\AuthController@dataUser');
//Obtener servicios
Route::get('services', 'Api\ServicesController@index');
//Obtener usuarios asociados a un servicio
Route::get('services_user', 'Api\ServicesUserController@users');
//Obtener contrataciones
Route::get('hiring', 'Api\HiringsController@index');
//Ver resumen de una contratación
Route::get('hiring/{id}', 'Api\HiringsController@show');
//Ver notificaciones de un usuario
Route::get('notification/{id}/is_worker/{worker}', 'Api\NotificationController@getNotifications');

//Obtener localidades
Route::get('locations', 'Api\LocationController@index');
Route::post('userLastAction', 'Api\AuthController@storeLastAction');

//contar cantidad de solicitudes
Route::post('/countActive/hiring', 'Api\HiringsController@countActiveHiring');

Route::post('/contact-review/check', 'Api\ContactReviewController@checkContactReview');
Route::post('/contact-review/first-question-answer', 'Api\ContactReviewController@answerFirstQuestion');
Route::post('/contact-review/second-question-answer', 'Api\ContactReviewController@answerSecondQuestion');

Route::post("/favorite/fetch", "Api\FavoriteController@fetch");
Route::post("/favorite/store", "Api\FavoriteController@store");
Route::post("/favorite/check", "Api\FavoriteController@check");
Route::post("/favorite/delete", "Api\FavoriteController@delete");

Route::post("/hiring-history/delete-all", 'Api\HiringsController@deleteAllHistories');
Route::post("/hiring-history/delete", 'Api\HiringsController@deleteHistory');

Route::group(['middleware'=>'jwt.auth'], function () {

  Route::post('/changeAddress', 'Api\AuthController@changeAddress');

  Route::get('users', 'Api\AuthController@index');
  Route::delete('users/{id}', 'Api\AuthController@delete');
  Route::get('users/{id}/restore', 'Api\AuthController@restore');

  //Crear contratacion
  Route::post('hiring', 'Api\HiringsController@store');
  //Crear contratacion
  Route::post('hiring_mt4', 'Api\HiringsController@mt4');
  //Actualizar estado contratacion
  Route::put('hiring', 'Api\HiringsController@update');
  Route::post('hiring/contact', 'Api\HiringsController@storeContact');

  //Actualizar estado de notificación
  Route::put('notification/{id}', 'Api\NotificationController@markRead');
  //Actualizar mostrar mapa
  Route::post('hiring/update/map/{hiring_id}', 'Api\HiringsController@updateShowMap');
  //Obtiene mapas
  Route::get('hiring/get/map/{hiring_id}', 'Api\HiringsController@getMaps');
  //Obtiene posiciónde usuarios
  Route::post('hiring/get/position', 'Api\HiringsController@getUserPosition');

  //Crear servicio
  Route::post('services', 'Api\ServicesController@store');
  //Actualizar servicio
  Route::put('services', 'Api\ServicesController@update');
  //Borrar servicio
  Route::delete('services/{id}', 'Api\ServicesController@delete');
  //Asociar servicio a usuario autenticado
  Route::post('services_user', 'Api\ServicesUserController@store');

  //Config application
  Route::get('config', 'Api\ConfigController@index');
  Route::post('config', 'Api\ConfigController@store');


  //Crear localidad
  Route::post('locations', 'Api\LocationController@store');
  //buscar localidad
  Route::get('location/{id}', 'Api\LocationController@find');
  //Actualizar una localidad
  Route::put('locations/{id}', 'Api\LocationController@update');
  //Eliminar una localidad
  Route::delete('locations/{id}', 'Api\LocationController@delete');

});//middle auth

//Publicidad
Route::post('/ads', 'Api\AdsController@index');

//Administrador de publicidad
Route::post('/administrator/ad/store', 'Api\AdministratorController@storeAd');
Route::get('/administrator/ad/getType', 'Api\AdministratorController@getAdTypes');
Route::get('/administrator/ad/location/{location_id}', 'Api\AdministratorController@getAds');
Route::delete('/administrator/ad/delete/{id}', 'Api\AdministratorController@deleteAd');

Route::post("/message/store", "ChatController@store");
Route::post("/message/fetch/chat", "ChatController@fetch");
Route::post("/message/delete", "ChatController@deleteMessage");
Route::post("/message/conversation/delete", "ChatController@deleteConversation");
Route::post("/message/conversation/delete/all", "ChatController@deleteAll");
Route::post("/my-chats", "ChatController@chats");

