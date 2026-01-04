<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyOperationsReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public $confirmedCount,
        public $pendingCount,
        public $cancelledCount,
        public string $excelPath
    ) {}

    public function envelope(): Envelope
    {
        $date = now()->format('Y-m-d');

        return new Envelope(
            subject: "Daily Operations Report - {$date}",
            replyTo: [config('booking.email_config.reply_to', 'info@greenvacationscr.com')],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.daily-operations',
            with: [
                'confirmedCount' => $this->confirmedCount,
                'pendingCount' => $this->pendingCount,
                'cancelledCount' => $this->cancelledCount,
                'date' => now()->format('F j, Y'),
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->excelPath)
                ->as('bookings-' . now()->format('Y-m-d') . '.xlsx')
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
