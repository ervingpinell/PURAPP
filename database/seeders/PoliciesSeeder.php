<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\Policy;
use App\Models\PolicyTranslation;

class PoliciesSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today()->toDateString();

        // 1) Cancelación (default)
        $p1 = Policy::create([
            'type'           => 'cancelacion',
            'name'           => 'Política de Cancelación (General)',
            'is_default'     => true,
            'is_active'      => true,
            'effective_from' => $today,
        ]);
        PolicyTranslation::create([
            'policy_id' => $p1->policy_id,
            'locale'    => 'es',
            'title'     => 'Política de Cancelación',
            'content'   => "• Cancelaciones con 24 horas o más de antelación: reembolso completo.\n• Cancelaciones entre 12 y 24 horas: reembolso del 50%.\n• Con menos de 12 horas o no presentación: no reembolsable.\n• Cambios de fecha sujetos a disponibilidad.\n• Las tarifas de terceros (hoteles, parques, etc.) pueden tener condiciones adicionales.",
        ]);

        // 2) Reembolsos
        $p2 = Policy::create([
            'type'           => 'reembolso',
            'name'           => 'Política de Reembolsos',
            'is_default'     => false,
            'is_active'      => true,
            'effective_from' => $today,
        ]);
        PolicyTranslation::create([
            'policy_id' => $p2->policy_id,
            'locale'    => 'es',
            'title'     => 'Política de Reembolsos',
            'content'   => "• Los reembolsos se procesan al método de pago original.\n• El tiempo de acreditación depende del banco/emisor.\n• Cargos de terceros no siempre son reembolsables.\n• Podemos solicitar documentación adicional para validar el reembolso.",
        ]);

        // 3) Términos
        $p3 = Policy::create([
            'type'           => 'terminos',
            'name'           => 'Términos y Condiciones',
            'is_default'     => false,
            'is_active'      => true,
            'effective_from' => $today,
        ]);
        PolicyTranslation::create([
            'policy_id' => $p3->policy_id,
            'locale'    => 'es',
            'title'     => 'Términos y Condiciones',
            'content'   => "• La compra de servicios implica la aceptación de estos términos.\n• Precios, horarios y rutas pueden variar por condiciones operativas.\n• El cliente debe proporcionar información veraz y actualizada.\n• Aplican restricciones por seguridad y normativa local.",
        ]);

        // 4) Privacidad
        $p4 = Policy::create([
            'type'           => 'privacidad',
            'name'           => 'Política de Privacidad',
            'is_default'     => false,
            'is_active'      => true,
            'effective_from' => $today,
        ]);
        PolicyTranslation::create([
            'policy_id' => $p4->policy_id,
            'locale'    => 'es',
            'title'     => 'Política de Privacidad',
            'content'   => "• Tratamos datos personales conforme a la normativa aplicable.\n• Usamos la información para gestionar reservas y comunicación.\n• Puedes ejercer derechos de acceso, rectificación y supresión.\n• Compartimos datos con terceros solo cuando es necesario para la operación.",
        ]);
    }
}
