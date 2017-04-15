<?php

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

/** @var \Illuminate\Routing\Router $router */
$router->post('login', 'Auth\LoginController@login');
$router->post('logout', 'Auth\LoginController@logout')->middleware('auth:api');
$router->post('register', 'Auth\RegisterController@register');

$router->post('password/email', 'Auth\PasswordController@sendPasswordResetLink');
$router->post('password/reset', 'Auth\PasswordController@resetPassword');

$router->get('activation', 'Auth\ActivateController@requestActivationCode')->middleware('auth:api');
$router->get('activation/{token}', 'Auth\ActivateController@activate');
$router->post('activation', 'Auth\ActivateController@activate');
