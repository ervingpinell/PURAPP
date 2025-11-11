<?php

namespace App\Services\Mail;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Symfony\Component\Mime\Address;

class GraphMailTransport extends AbstractTransport
{
    protected GraphAuthService $authService;
    protected string $senderUpn;
    protected ?string $replyTo;

    public function __construct(GraphAuthService $authService, string $senderUpn, ?string $replyTo = null)
    {
        parent::__construct();

        $this->authService = $authService;
        $this->senderUpn   = $senderUpn;
        $this->replyTo     = $replyTo;
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $payload = $this->buildPayload($email);

        $this->sendViaGraphApi($payload);
    }

    /**
     * Construye el payload para Microsoft Graph API
     */
    protected function buildPayload($email): array
    {
        $from = $email->getFrom();
        $fromAddress = $from ? $from[0]->getAddress() : $this->senderUpn;
        $fromName = $from ? ($from[0]->getName() ?: null) : null;

        $payload = [
            'message' => [
                'subject' => $email->getSubject() ?? '(No subject)',
                'body' => [
                    'contentType' => $email->getHtmlBody() ? 'HTML' : 'Text',
                    'content' => $email->getHtmlBody() ?? $email->getTextBody() ?? '',
                ],
                'from' => [
                    'emailAddress' => [
                        'address' => $fromAddress,
                    ],
                ],
                'toRecipients' => $this->formatRecipients($email->getTo()),
            ],
            'saveToSentItems' => true,
        ];

        // Agregar nombre del remitente si existe
        if ($fromName) {
            $payload['message']['from']['emailAddress']['name'] = $fromName;
        }

        // CC
        if ($email->getCc()) {
            $payload['message']['ccRecipients'] = $this->formatRecipients($email->getCc());
        }

        // BCC
        if ($email->getBcc()) {
            $payload['message']['bccRecipients'] = $this->formatRecipients($email->getBcc());
        }

        // Reply-To (prioridad: 1) email replyTo, 2) config global)
        $replyToAddresses = $email->getReplyTo();
        if ($replyToAddresses && count($replyToAddresses) > 0) {
            $payload['message']['replyTo'] = $this->formatRecipients($replyToAddresses);
        } elseif ($this->replyTo) {
            $payload['message']['replyTo'] = [
                ['emailAddress' => ['address' => $this->replyTo]],
            ];
        }

        // Attachments
        if ($email->getAttachments()) {
            $payload['message']['attachments'] = $this->formatAttachments($email->getAttachments());
        }

        return $payload;
    }

    /**
     * Formatea destinatarios para Graph API
     */
    protected function formatRecipients(array $addresses): array
    {
        return collect($addresses)->map(function (Address $address) {
            $recipient = ['emailAddress' => ['address' => $address->getAddress()]];

            if ($name = $address->getName()) {
                $recipient['emailAddress']['name'] = $name;
            }

            return $recipient;
        })->values()->all();
    }

    /**
     * Formatea attachments para Graph API
     */
    protected function formatAttachments(array $attachments): array
    {
        return collect($attachments)->map(function ($attachment) {
            return [
                '@odata.type' => '#microsoft.graph.fileAttachment',
                'name' => $attachment->getPreparedHeaders()->getHeaderParameter('Content-Disposition', 'filename')
                         ?? 'attachment',
                'contentType' => $attachment->getMediaType() . '/' . $attachment->getMediaSubtype(),
                'contentBytes' => base64_encode($attachment->getBody()),
            ];
        })->values()->all();
    }

    /**
     * Envía el correo mediante Microsoft Graph API
     */
    protected function sendViaGraphApi(array $payload): void
    {
        $token = $this->authService->getAccessToken();
        $url = "https://graph.microsoft.com/v1.0/users/{$this->senderUpn}/sendMail";

        try {
            $response = Http::withToken($token)
                ->timeout(30)
                ->post($url, $payload);

            if ($response->successful()) {
                Log::info('Graph Mail: Correo enviado exitosamente', [
                    'from' => $this->senderUpn,
                    'to' => collect($payload['message']['toRecipients'] ?? [])
                        ->pluck('emailAddress.address')
                        ->implode(', '),
                    'subject' => $payload['message']['subject'] ?? '(No subject)',
                ]);
                return;
            }

            // Si hay error 401, limpiar token y reintentar una vez
            if ($response->status() === 401) {
                Log::warning('Graph Mail: Token expirado, reintentando...');
                $this->authService->clearToken();

                $newToken = $this->authService->getAccessToken();
                $response = Http::withToken($newToken)
                    ->timeout(30)
                    ->post($url, $payload);

                if ($response->successful()) {
                    Log::info('Graph Mail: Correo enviado en segundo intento');
                    return;
                }
            }

            // Error definitivo
            $errorBody = $response->json();
            $errorMessage = $errorBody['error']['message'] ?? $response->body();

            Log::error('Graph Mail: Error al enviar correo', [
                'status' => $response->status(),
                'error' => $errorMessage,
                'from' => $this->senderUpn,
                'payload' => $payload,
            ]);

            throw new \RuntimeException("Error enviando correo via Graph: {$errorMessage}");

        } catch (\Throwable $e) {
            Log::error('Graph Mail: Excepción al enviar', [
                'error' => $e->getMessage(),
                'from' => $this->senderUpn,
            ]);
            throw $e;
        }
    }

    public function __toString(): string
    {
        return 'graph';
    }
}
