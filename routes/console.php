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

/*
|--------------------------------------------------------------------------
| Limpieza de carritos expirados
|--------------------------------------------------------------------------
| Limpia carritos y reservas expiradas para liberar capacidad.
| Se ejecuta cada hora para mantener la base de datos limpia.
*/
Schedule::command('cart:cleanup-reservations')
    ->hourly()
    ->timezone(config('app.timezone', 'UTC'))
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/cart-cleanup.log'));

/*
|--------------------------------------------------------------------------
| Limpieza de pagos expirados/trabados (Job)
|--------------------------------------------------------------------------
| Revisa pagos 'pending'/'processing' vencidos y los marca como failed.
*/
Schedule::job(new \App\Jobs\ExpirePendingPayments)
    ->everyThirtyMinutes()
    ->timezone(config('app.timezone', 'UTC'))
    ->onOneServer();

/*
|--------------------------------------------------------------------------
| Cancel Expired Unpaid Bookings (Pay-Later System)
|--------------------------------------------------------------------------
| Cancela bookings pendientes sin pagar que hayan expirado.
| Se ejecuta cada 5 minutos para liberar capacidad rápidamente.
*/
Schedule::command('bookings:cancel-expired-unpaid')
    ->everyFiveMinutes()
    ->timezone(config('app.timezone', 'UTC'))
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/cancel-expired-unpaid.log'));

/*
|--------------------------------------------------------------------------
| Send Expiry Warnings (Pay-Later System)
|--------------------------------------------------------------------------
| Envía alertas al admin 2h antes de que expire un booking sin pagar.
| Se ejecuta cada 30 minutos para notificaciones oportunas.
*/
Schedule::command('bookings:send-expiry-warnings')
    ->everyThirtyMinutes()
    ->timezone(config('app.timezone', 'UTC'))
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/expiry-warnings.log'));

/*
|--------------------------------------------------------------------------
| Process Auto-Charges (Pay-Later System)
|--------------------------------------------------------------------------
| Procesa cobros automáticos y envía recordatorios de pago.
| Se ejecuta diariamente a las 3:00 AM.
*/
Schedule::command('bookings:process-auto-charges')
    ->dailyAt('03:00')
    ->timezone(config('app.timezone', 'UTC'))
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/auto-charges.log'));

/*
|--------------------------------------------------------------------------
| Cancel Unpaid Bookings Before Tour
|--------------------------------------------------------------------------
| Cancela reservas sin pagar X horas antes del tour (configurable).
| Se ejecuta cada 30 minutos para verificar (tours pueden iniciar a :30).
*/
Schedule::command('bookings:cancel-unpaid-before-product')
    ->everyThirtyMinutes()
    ->timezone(config('app.timezone', 'UTC'))
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/cancel-before-tour.log'));

/*
|--------------------------------------------------------------------------
| Daily Operations Report
|--------------------------------------------------------------------------
| Envía reporte diario en Excel con todas las reservas del día.
| Hora configurable en settings (default: 06:00).
*/
$reportTime = setting('booking.operations_report_time', '06:00');
Schedule::command('bookings:send-daily-operations-report')
    ->dailyAt($reportTime)
    ->timezone(config('app.timezone', 'UTC'))
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/operations-report.log'));

/*
|--------------------------------------------------------------------------
| Sync External Reviews (Viator Compliance)
|--------------------------------------------------------------------------
| Sincroniza reviews de proveedores externos (Viator, GYG, etc.) semanalmente.
| Cumple con requisitos de Viator: sync semanal y auto-eliminación.
| Se ejecuta domingos a las 2:00 AM para evitar conflictos con otras tareas.
*/
Schedule::command('reviews:sync --all')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->timezone(config('app.timezone', 'UTC'))
    ->onOneServer()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/reviews-sync.log'));

/*
|--------------------------------------------------------------------------
| Prune Review Requests
|--------------------------------------------------------------------------
| Permanently delete soft-deleted review requests older than 30 days.
| Keeps database clean and respects loop prevention logic (trash vs skip).
*/
Schedule::command('reviews:prune-requests --days=30')
    ->dailyAt('04:00')
    ->timezone(config('app.timezone', 'UTC'))
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/prune-reviews.log'));
