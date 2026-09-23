<?php

use Illuminate\Support\Facades\Route;
use App\Models\Producto;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/productos', function () {
    $productos = Producto::all();

    return view('productos', compact('productos'));
});