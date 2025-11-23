<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console routes & scheduled tasks
|--------------------------------------------------------------------------
| Este archivo se carga por el scheduler de Laravel.
| Recuerda configurar el CRON del sistema para ejecutar:
| * * * * * php /ruta/a/tu/proyecto/artisan schedule:run >> /dev/null 2>&1
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Prune de usuarios no verificados
|--------------------------------------------------------------------------
| Requiere que tu modelo User implemente ->prunable() (ya lo tienes).
| Se ejecuta a las 02:30 (hora de config('app.timezone')) y registra salida.
*/
Schedule::command('model:prune', [
    '--model' => \App\Models\User::class,
])
    ->dailyAt('02:30')
    ->timezone(config('app.timezone', 'UTC'))
    ->onOneServer() // seguro si tienes varios workers/servidores
    ->appendOutputTo(storage_path('logs/prune.log'));

/*
|--------------------------------------------------------------------------
| Limpieza de reservas expiradas
|--------------------------------------------------------------------------
| Cancela reservas pendientes sin pago después del timeout configurado.
| Se ejecuta cada hora para liberar cupos de capacidad.
*/
Schedule::command('bookings:cleanup-expired', ['--force'])
    ->hourly()
    ->timezone(config('app.timezone', 'UTC'))
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/booking-cleanup.log'));
