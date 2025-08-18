<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        Faq::create([
            'question' => '¿Cómo puedo hacer una reservación?',
            'answer' => 'Debes registrarte en nuestro sitio, agregar el tour deseado a tu carrito y enviar la solicitud de reserva. Recibirás un correo con los detalles y pasos a seguir.',
        ]);

        Faq::create([
            'question' => '¿Puedo cancelar o modificar una reserva?',
            'answer' => 'Sí. Puedes contactarnos hasta 24 horas antes del inicio del tour para cancelar o modificar sin penalización. Pasado ese plazo, se aplicará un 100% de penalidad.',
        ]);

        Faq::create([
            'question' => '¿Los precios incluyen transporte?',
            'answer' => 'Sí el precio incuye el transporte. Sin embargo no se ofrecen descuentos en caso de que no lo utilicen, es una cortesía. Te recomendamos revisar la lista de hoteles donde ofrecemos transporte',
        ]);

        Faq::create([
            'question' => '¿Necesito pagar en línea?',
            'answer' => 'Siempre se recomienda pagar en línea para asegurar tu espacio. Si tienes algún problema para realizarlo, contáctanos.',
        ]);

        Faq::create([
            'question' => '¿Hay descuentos para niños o grupos?',
            'answer' => 'Sí, ofrecemos descuentos especiales para niños y grupos grandes. Contáctanos para más detalles según el tour que te interese.',
        ]);

                Faq::create([
            'question' => '¿El agua de La Fortuna es potable?',
            'answer' => 'Sí, en Costa Rica la gran mayoría de agua es potable (recomendamos consultar fuentes locales). Pero al menos en La Fortuna, contamos con agua de excelente calidad.',
        ]);

                        Faq::create([
            'question' => '¿El tour incluye agua?',
            'answer' => 'Sí, el tour incluye agua embotellada para todos los participantes. Sin embargo, recomendamos traer su propia botella rellenable para reducir la contaminación plástica.',
        ]);
        Faq::create([
            'question' => '¿Necesito traer mi pasaporte?',
            'answer' => 'No es necesario, pero se recomienda llevar una copia en caso de emergencias.',
        ]);

        Faq::create([
            'question' => '¿Puedo dejar mis pertenencias en el transporte?',
            'answer' => 'Claro! Generalmente es muy seguro y puedes dejar tus pertenencias en el transporte, pero te recomendamos no dejar objetos de valor y llevar contigo lo esencial durante el tour.',
        ]);
            Faq::create([
            'question' => '¿Qué tipo de ropa debo traer para el Safari?',
            'answer' => 'Recomendamos traer cualquier tipo de ropa cómoda, sandalias o zapatos de agua, ropa extra, tu propia botella de agua, toallas, sombreros, protector solar, binoculares, cámaras, bolsas impermeables, repelente de insectos y cualquier otro artículo personal que puedas necesitar.',
        ]);
                    Faq::create([
            'question' => '¿Qué tipo de ropa debo traer para las caminatas?',
            'answer' => 'Recomendamos traer cualquier tipo de ropa cómoda, zapatos cerrados (obligatorios en algunos parques), sombrillas, ponchos, sombreros, protector solar, binoculares, cámaras, bolsas impermeables, botellas de agua (no plásticas), repelente de insectos y cualquier otro artículo personal que puedas necesitar.',
        ]);
                            Faq::create([
            'question' => '¿Hay casilleros para dejar las cosas?',
            'answer' => 'Lamentablemente no contamos con casilleros',
        ]);
    }
}
