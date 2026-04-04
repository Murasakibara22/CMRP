<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return redirect()->route('login');
});

Route::prefix('auth')->group(function () {


    // si deja connecté rediriger sur la page de admin
    if (auth()->check()) {
        return redirect()->route('admin.home');
    }

    Route::livewire('connexion', 'auth::login')->name('login');

});



Route::middleware('auth')->group(function () {

    Route::prefix('admin')->as('admin.')->group(function () {

        Route::livewire('dashboard', 'admin::dashboard.index')->name('home');
        Route::livewire('fideles', 'admin::fidele.index')->name('membres.index');
        Route::livewire('type-cotisations', 'admin::typecotisation.index')->name('type-cotisations.index');
        Route::livewire('cotisations', 'admin::cotisation.index')->name('cotisations.index');

    });
});
