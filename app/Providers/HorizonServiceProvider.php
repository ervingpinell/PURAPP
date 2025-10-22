<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\HorizonApplicationServiceProvider;

class HorizonServiceProvider extends HorizonApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     */
public function boot()
{
    $this->gate();

    Horizon::auth(function ($request) {
        // Permitir solo correos específicos
        return in_array(optional($request->user())->email, [
            'admin@greenvacationscr.com',
            'dev@greenvacationscr.com',
            'ervingpinell@gmail.com',
            'axelpaniaguab54@gmail.com'
        ]);
    });
}

    /**
     * Register the Horizon gate.
     *
     * This gate determines who can access Horizon in non-local environments.
     */
protected function gate()
{
    Gate::define('viewHorizon', function ($user) {
        return in_array($user->email, [
            'admin@greenvacationscr.com',
            'dev@greenvacationscr.com',
            'ervingpinell@gmail.com',
            'axelpaniaguab54@gmail.com'
        ]);
    });
}
}
