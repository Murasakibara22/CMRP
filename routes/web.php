<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;




Route::get('/', function () {
    return redirect()->route('login-user');
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/login', function () {
    return redirect()->route('login-user');
})->name('login');

Route::prefix('auth')->group(function () {


    // si deja connecté rediriger sur la page de admin
    if (auth()->check()) {
        return redirect()->route('admin.home');
    }

    Route::livewire('connexion', 'auth::login')->name('login-admin');

});

Route::prefix('sign')->group(function () {


    // si deja connecté rediriger sur la page de admin


    Route::livewire('connexion/fidele', 'auth::login-frontend')->name('login-user')->middleware('check.customer.login');

});


Route::group(['middleware' => 'customer.verify'], function () {


    Route::prefix('customer')->as('customer.')->group(function () {
        Route::livewire('dashboard', 'frontend::home.index')->name('home');
        Route::livewire('cotisations', 'frontend::cotisation.index')->name('cotisations');
        Route::livewire('ajout-cotisations', 'frontend::add-cotisation.index')->name('ajout-cotisations');
        Route::livewire('paiements', 'frontend::paiement.index')->name('paiements');
        Route::livewire('profile', 'frontend::profile.index')->name('profile');
        Route::livewire('reclamations', 'frontend::reclammation.index')->name('reclamations');
        Route::livewire('documents', 'frontend::document.index')->name('documents');

    });
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
        Route::livewire('users', 'admin::user.index')->name('users.index');
        Route::livewire('messages', 'admin::messagegroupe.index')->name('messages.index');
        Route::livewire('reclamations', 'admin::reclammation.index')->name('reclamations.index');
        Route::livewire('roles', 'admin::settingrole.index')->name('roles.index');
        Route::livewire('cout-engagement', 'admin::coutengagement.index')->name('cout-engagement.index');

    });
});


Route::get('/deconnexion', function () {
    if(auth()->guard('web')->check()){

        // ActivityLog("Deconnexion", "Admin");

        auth()->logout();
        return redirect('/');
    }

    if(auth()->guard('customer')->check()){
        auth()->guard('customer')->logout();
        return redirect('/');
    }

})->name('deconnexion');
