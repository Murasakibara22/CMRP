<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return redirect()->route('login-user');
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

Route::prefix('sign')->group(function () {


    // si deja connecté rediriger sur la page de admin
    if (auth('customer')->check()) {
        return redirect()->route('customer.home');
    }

    Route::livewire('connexion/fidele', 'auth::login-frontend')->name('login-user');
    Route::livewire('otp/validation', 'auth::otp-frontend')->name('otp-user');
    Route::livewire('inscription/fidele', 'auth::inscription-frontend')->name('register-user');

});


// Route::middleware('auth:customer')->group(function () {

//     Route::prefix('customer')->as('customer.')->group(function () {
        Route::livewire('dashboard', 'frontend::home.index')->name('home');
        Route::livewire('cotisations', 'frontend::cotisation.index')->name('cotisations');
        Route::livewire('ajout-cotisations', 'frontend::add-cotisation.index')->name('ajout-cotisations');
        Route::livewire('paiements', 'frontend::paiement.index')->name('paiements');
        Route::livewire('profile', 'frontend::profile.index')->name('profile');
        Route::livewire('reclamations', 'frontend::reclammation.index')->name('reclamations');

//     });
// });


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
        Route::livewire('users', 'admin::user.index')->name('users.index');

    });
});
