<?php

use Illuminate\Routing\Router;

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

/*------------------------------------------------------------------------
 | Non-localized, generic routes (such as those for admin panel etc).
 *-----------------------------------------------------------------------*/

/** @var \Illuminate\Routing\Router $router */
$router->get('oauth/to/{provider}', ['uses' => 'Auth\LoginController@handleOAuthRedirect', 'as' => 'oauth.to']);
$router->get('oauth/from/{provider}', ['uses' => 'Auth\LoginController@handleOAuthReturn', 'as' => 'oauth.from']);
$router->get('admin', 'AdminController@index');

/*---------------------------------------------------------------------------------------------------------
 | Register localized routes with locale-prefices (in case of default locale, no prefix is attached).
 *--------------------------------------------------------------------------------------------------------*/

foreach (config('app.locales') as $prefix => $localeName) {
    app('translator')->setLocale($prefix);
    // Localized routes.
    $router->group(compact('namespace', 'middleware', 'prefix'), function (Router $router) use ($prefix) {
        $router->get('login', ['uses' => 'Auth\LoginController@showLoginForm', 'as' => 'login', 'locale' => $prefix]);
        $router->get('logout', ['uses' => 'Auth\LoginController@logout', 'as' => 'logout', 'locale' => $prefix])->middleware('auth');
        $router->get('register', ['uses' => 'Auth\RegisterController@showRegistrationForm', 'as' => 'register', 'locale' => $prefix]);
        $router->get('password/email', ['uses' => 'Auth\PasswordController@requestPasswordResetLink', 'as' => 'password.email', 'locale' => $prefix]);
        $router->get('password/reset/{token}', ['uses' => 'Auth\PasswordController@showPasswordResetForm', 'as' => 'password.reset', 'locale' => $prefix]);
        $router->get('activation', ['uses' => 'Auth\ActivateController@requestActivationCode', 'as' => 'activation.request', 'locale' => $prefix])->middleware('auth');
        $router->get('activation/{token}', ['uses' => 'Auth\ActivateController@activate', 'as' => 'activation.complete', 'locale' => $prefix]);
        $router->resource('files', 'FilesController', ['only' => ['index', 'create', 'store', 'show', 'destroy']]);
        $router->get('', ['uses' => 'HomeController@index', 'as' => 'home', 'locale' => $prefix]);
    });
}

/*------------------------------------
 | Non-localized routes
 *-----------------------------------*/
$router->post('login', 'Auth\LoginController@loginViaWeb');
$router->post('register', 'Auth\RegisterController@register');
$router->post('password/email', 'Auth\PasswordController@sendPasswordResetLink');
$router->post('password/reset', 'Auth\PasswordController@resetPassword');
$router->post('activation', 'Auth\ActivateController@activate');

/*------------------------------------
 | Default, non-localized home
 *-----------------------------------*/

$router->get('home', 'HomeController@index');
$router->get('', 'HomeController@index');
