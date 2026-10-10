<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/admin/login', '/login');
Route::redirect('/admin/register', '/register');
Route::redirect('/manager/login', '/login');
Route::redirect('/broker/login', '/login');
