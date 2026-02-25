<?php

use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\Elevador::class)->name('elevador');


Route::get('{any}', function () {
    return redirect()->route('elevador');
})->where('any', '.*');