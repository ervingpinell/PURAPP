<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateContent;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $templates = $this->getTemplates();

        foreach ($templates as $templateData) {
            $template = EmailTemplate::create([
                'template_key' => $templateData['key'],
                'name' => $templateData['name'],
                'description' => $templateData['description'],
                'category' => $templateData['category'],
                'is_active' => true,
            ]);

            foreach ($templateData['content'] as $locale => $content) {
                EmailTemplateContent::create([
                    'email_template_id' => $template->id,
                    'locale' => $locale,
                    'subject' => $content['subject'],
                    'content' => $content['sections'],
                ]);
            }
        }
    }

    /**
     * Get all template definitions.
     */
    protected function getTemplates(): array
    {
        return [
            // CUSTOMER EMAILS
            [
                'key' => 'booking_created_customer',
                'name' => 'Booking Created (Customer)',
                'description' => 'Sent to customer when a new booking is created',
                'category' => 'customer',
                'content' => [
                    'es' => [
                        'subject' => 'Reserva Creada - {{booking_reference}}',
                        'sections' => [
                            'greeting' => '¡Hola {{customer_name}}!',
                            'intro' => 'Tu reserva ha sido creada exitosamente.',
                            'payment_cta' => 'Pagar Ahora',
                            'account_setup_title' => '¡Crea tu cuenta!',
                            'account_setup_intro' => 'Configura una contraseña para acceder a todos los beneficios',
                            'account_setup_cta' => 'Crear Mi Cuenta',
                        ],
                    ],
                    'en' => [
                        'subject' => 'Booking Created - {{booking_reference}}',
                        'sections' => [
                            'greeting' => 'Hello {{customer_name}}!',
                            'intro' => 'Your booking has been created successfully.',
                            'payment_cta' => 'Pay Now',
                            'account_setup_title' => 'Create Your Account!',
                            'account_setup_intro' => 'Set up a password to access all benefits',
                            'account_setup_cta' => 'Create My Account',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'booking_confirmed',
                'name' => 'Booking Confirmed',
                'description' => 'Sent when booking is confirmed by admin',
                'category' => 'customer',
                'content' => [
                    'es' => [
                        'subject' => 'Reserva Confirmada - {{booking_reference}}',
                        'sections' => [
                            'greeting' => '¡Hola {{customer_name}}!',
                            'confirmation_message' => 'Tu reserva ha sido confirmada.',
                            'next_steps_title' => 'Próximos pasos',
                        ],
                    ],
                    'en' => [
                        'subject' => 'Booking Confirmed - {{booking_reference}}',
                        'sections' => [
                            'greeting' => 'Hello {{customer_name}}!',
                            'confirmation_message' => 'Your booking has been confirmed.',
                            'next_steps_title' => 'Next steps',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'payment_success',
                'name' => 'Payment Success',
                'description' => 'Sent when payment is completed successfully',
                'category' => 'customer',
                'content' => [
                    'es' => [
                        'subject' => 'Pago Recibido - {{booking_reference}}',
                        'sections' => [
                            'greeting' => '¡Hola {{customer_name}}!',
                            'success_message' => 'Hemos recibido tu pago exitosamente.',
                            'amount_label' => 'Monto pagado',
                            'receipt_cta' => 'Ver Recibo',
                        ],
                    ],
                    'en' => [
                        'subject' => 'Payment Received - {{booking_reference}}',
                        'sections' => [
                            'greeting' => 'Hello {{customer_name}}!',
                            'success_message' => 'We have received your payment successfully.',
                            'amount_label' => 'Amount paid',
                            'receipt_cta' => 'View Receipt',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'contact_message',
                'name' => 'Contact Form Message',
                'description' => 'Sent when someone submits the contact form',
                'category' => 'other',
                'content' => [
                    'es' => [
                        'subject' => 'Nuevo mensaje de contacto',
                        'sections' => [
                            'intro' => 'Has recibido un nuevo mensaje de contacto:',
                            'from_label' => 'De',
                            'email_label' => 'Email',
                            'message_label' => 'Mensaje',
                        ],
                    ],
                    'en' => [
                        'subject' => 'New contact message',
                        'sections' => [
                            'intro' => 'You have received a new contact message:',
                            'from_label' => 'From',
                            'email_label' => 'Email',
                            'message_label' => 'Message',
                        ],
                    ],
                ],
            ],

            // FORTIFY / AUTHENTICATION EMAILS
            [
                'key' => 'password_reset',
                'name' => 'Password Reset',
                'description' => 'Sent when user requests password reset',
                'category' => 'customer',
                'content' => [
                    'es' => [
                        'subject' => 'Restablecer Contraseña',
                        'sections' => [
                            'greeting' => '¡Hola!',
                            'intro' => 'Recibimos una solicitud para restablecer tu contraseña.',
                            'action_text' => 'Restablecer Contraseña',
                            'expiry_notice' => 'Este enlace expirará en {{count}} minutos.',
                            'ignore_notice' => 'Si no solicitaste restablecer tu contraseña, puedes ignorar este correo.',
                        ],
                    ],
                    'en' => [
                        'subject' => 'Reset Password',
                        'sections' => [
                            'greeting' => 'Hello!',
                            'intro' => 'You are receiving this email because we received a password reset request for your account.',
                            'action_text' => 'Reset Password',
                            'expiry_notice' => 'This password reset link will expire in {{count}} minutes.',
                            'ignore_notice' => 'If you did not request a password reset, no further action is required.',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'email_verification',
                'name' => 'Email Verification',
                'description' => 'Sent to verify user email address',
                'category' => 'customer',
                'content' => [
                    'es' => [
                        'subject' => 'Verifica tu Correo Electrónico',
                        'sections' => [
                            'greeting' => '¡Hola!',
                            'intro' => 'Por favor, haz clic en el botón de abajo para verificar tu dirección de correo electrónico.',
                            'action_text' => 'Verificar Correo',
                            'ignore_notice' => 'Si no creaste una cuenta, puedes ignorar este correo.',
                        ],
                    ],
                    'en' => [
                        'subject' => 'Verify Email Address',
                        'sections' => [
                            'greeting' => 'Hello!',
                            'intro' => 'Please click the button below to verify your email address.',
                            'action_text' => 'Verify Email Address',
                            'ignore_notice' => 'If you did not create an account, no further action is required.',
                        ],
                    ],
                ],
            ],
            [
                'key' => 'two_factor_code',
                'name' => 'Two-Factor Authentication Code',
                'description' => 'Sent with 2FA code for admin login',
                'category' => 'admin',
                'content' => [
                    'es' => [
                        'subject' => 'Código de Autenticación',
                        'sections' => [
                            'greeting' => '¡Hola!',
                            'intro' => 'Tu código de autenticación de dos factores es:',
                            'code_label' => 'Código',
                            'expiry_notice' => 'Este código expirará en 10 minutos.',
                        ],
                    ],
                    'en' => [
                        'subject' => 'Authentication Code',
                        'sections' => [
                            'greeting' => 'Hello!',
                            'intro' => 'Your two-factor authentication code is:',
                            'code_label' => 'Code',
                            'expiry_notice' => 'This code will expire in 10 minutes.',
                        ],
                    ],
                ],
            ],

            // BASE LAYOUT TEMPLATE
            [
                'key' => 'email_base_layout',
                'name' => 'Base Email Layout',
                'description' => 'Common header and footer for all emails',
                'category' => 'other',
                'content' => [
                    'es' => [
                        'subject' => 'N/A',
                        'sections' => [
                            'company_name' => 'Green Vacations CR',
                            'footer_text' => 'Gracias por elegirnos para tu aventura en Costa Rica',
                            'contact_label' => 'Contáctanos',
                            'unsubscribe_text' => 'Si no deseas recibir estos correos, puedes',
                            'unsubscribe_link_text' => 'darte de baja',
                            'copyright' => '© {{year}} Green Vacations CR. Todos los derechos reservados.',
                        ],
                    ],
                    'en' => [
                        'subject' => 'N/A',
                        'sections' => [
                            'company_name' => 'Green Vacations CR',
                            'footer_text' => 'Thank you for choosing us for your Costa Rica adventure',
                            'contact_label' => 'Contact Us',
                            'unsubscribe_text' => 'If you no longer wish to receive these emails, you can',
                            'unsubscribe_link_text' => 'unsubscribe',
                            'copyright' => '© {{year}} Green Vacations CR. All rights reserved.',
                        ],
                    ],
                ],
            ],
            // Add more templates as needed...
        ];
    }
}
