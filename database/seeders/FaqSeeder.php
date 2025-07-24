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
            'answer' => 'Se recomienda siempre pagar en línea para asegurar tu espacio. Si tienes algun problema para realizarlo, contactanos',
        ]);

        Faq::create([
            'question' => '¿Hay descuentos para niños o grupos?',
            'answer' => 'Sí, ofrecemos descuentos especiales para niños y grupos grandes. Contáctanos para más detalles según el tour que te interese.',
        ]);

                Faq::create([
            'question' => '¿Hay descuentos para niños o grupos?',
            'answer' => 'Sí, ofrecemos descuentos especiales para niños y grupos grandes. Contáctanos para más detalles según el tour que te interese.',
        ]);

                        Faq::create([
            'question' => '¿Qué tipo de ropa traer?',
            'answer' => 'Recomendamos traer cualquier tipo de ropa cómoda, solamente asegurate de utilizar un buen repelente de mosquitos y bloqueador solar. En el caso de los tours de caminatas, los zapatos cerrados son obligatorios.',
        ]);
    }
}
