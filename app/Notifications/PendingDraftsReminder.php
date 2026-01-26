<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PendingDraftsReminder extends Notification
{
    use Queueable;

    protected $drafts;
    protected $daysOld;

    /**
     * Create a new notification instance.
     */
    public function __construct($drafts, int $daysOld = 7)
    {
        $this->drafts = $drafts;
        $this->daysOld = $daysOld;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $count = $this->drafts->count();

        $message = (new MailMessage)
            ->subject("Tienes {$count} tour(es) en borrador sin completar")
            ->greeting("¡Hola {$notifiable->name}!")
            ->line("Tienes **{$count} tour(es) en borrador** que no has completado.");

        // Si hay solo un draft
        if ($count === 1) {
            $draft = $this->drafts->first();
            $message->line("📝 **{$draft->name}**")
                   ->line("Paso actual: {$draft->current_step}/6")
                   ->line("Última actualización: {$draft->updated_at->diffForHumans()}")
                   ->action('Continuar Editando', route('admin.products.wizard.continue', $draft));
        } else {
            // Si hay múltiples drafts
            $message->line("Estos son tus borradores pendientes:");

            foreach ($this->drafts->take(5) as $draft) {
                $message->line("• **{$draft->name}** (Paso {$draft->current_step}/6) - {$draft->updated_at->diffForHumans()}");
            }

            if ($count > 5) {
                $message->line("... y " . ($count - 5) . " más");
            }

            $message->action('Ver Mis Borradores', route('admin.products.wizard.create'));
        }

        $message->line('💡 **Consejo:** Completa o elimina tus borradores para mantener tu espacio organizado.')
                ->line('Los borradores sin actividad por más de 30 días serán eliminados automáticamente.')
                ->salutation('Equipo de ' . config('app.name'));

        return $message;
    }

    /**
     * Get the array representation of the notification (para database).
     */
    public function toArray($notifiable): array
    {
        $count = $this->drafts->count();

        return [
            'type' => 'pending_drafts_reminder',
            'drafts_count' => $count,
            'days_old' => $this->daysOld,
            'drafts' => $this->drafts->map(function ($draft) {
                return [
                    'product_id' => $draft->product_id,
                    'name' => $draft->name,
                    'current_step' => $draft->current_step,
                    'updated_at' => $draft->updated_at->toDateTimeString(),
                    'url' => route('admin.products.wizard.continue', $draft),
                ];
            })->toArray(),
            'message' => "Tienes {$count} tour(es) en borrador sin completar",
            'action_url' => route('admin.products.wizard.create'),
            'action_text' => 'Ver Borradores',
        ];
    }

    /**
     * Get the database channel representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }
}
