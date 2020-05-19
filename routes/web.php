<?php

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

$router->get('/files', 'FileController@index');
$router->post('/files', 'FileController@store');
$router->get('/files/{file}', 'FileController@show');
$router->put('/files/{file}', 'FileController@update');
$router->patch('/files/{file}', 'FileController@update');
$router->delete('/files/{file}', 'FileController@destroy');