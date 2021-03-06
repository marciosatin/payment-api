<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'users'], function () use ($router) {
    $router->get('', 'UserController@index');
    $router->get('/{id}', 'UserController@show');
    $router->post('', 'UserController@store');
    
    $router->post('/sellers', 'SellerController@store');
});

$router->group(['prefix' => 'transactions', 'middleware' => 'transactionAuth'], function () use ($router) {
    $router->post('', 'TransactionController@create');
});
