<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/test', function () {
    return view('test');
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
        Route::livewire('paiements', 'admin::paiement.index')->name('paiements.index');
        Route::livewire('bilan', 'admin::bilan.index')->name('bilan.index');
        Route::livewire('type-depenses', 'admin::typedepense.index')->name('type-depenses.index');
        Route::livewire('depenses', 'admin::depense.index')->name('depenses.index');

    });
});
