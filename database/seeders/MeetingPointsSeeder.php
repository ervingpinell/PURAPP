<?php

namespace Database\Seeders;

use App\Models\MeetingPoint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MeetingPointsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $meetingPoints = [
            [
                'name' => 'Iglesia Católica de La Fortuna',
                'description' => 'Parque Central de La Fortuna, Alajuela, Costa Rica',
                'instructions' => 'Este es el punto de encuentro en la Iglesia Católica de La Fortuna. En los alrededores hay espacios de estacionamiento demarcados, los cuales tienen un costo que deberá cancelarse al retirar el vehículo. En las esquinas encontrará las máquinas para realizar el pago. Nuestro punto de encuentro se ubica en la calle situada entre el parque central y la iglesia.',
                'map_url' => 'https://maps.app.goo.gl/aKDcTN98pXuaUb7m7',
                'pickup_time' => 'TBD',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Oficina de Green Vacations Costa Rica',
                'description' => 'La Fortuna, Alajuela, Costa Rica',
                'instructions' => 'Nuestra oficina cuenta con parqueo gratuito para clientes. El estacionamiento se encuentra contiguo a la oficina y está delimitado con estacas de colores similares a las luces del semáforo (verde, amarillo y rojo), lo que facilita su identificación.',
                'map_url' => 'https://maps.app.goo.gl/R5buyYCzxjw6RD2C6',
                'pickup_time' => 'TBD',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Arenal 1968 Volcano View and Lava Trails',
                'description' => 'Arenal 1968, La Fortuna, Alajuela, Costa Rica',
                'instructions' => 'En este lugar se realiza la caminata al volcán. Puede dejar su vehículo estacionado de forma gratuita durante todo el día. Al ingresar, es importante informar al personal de seguridad que viene con Green Vacations y que estará esperando dentro del parque. Deberá aguardar la llegada del guía para hacer uso de las instalaciones (excepto los baños). En caso contrario, el personal de Arenal 1968 podría cobrarle cualquier servicio que utilice.',
                'map_url' => 'https://maps.app.goo.gl/VF8NBcDWRqSyAyRs6',
                'pickup_time' => 'TBD',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        $translator = app(\App\Services\DeepLTranslator::class);
        $targetLocales = ['en', 'fr', 'pt', 'de'];

        foreach ($meetingPoints as $pointData) {
            // 1. Create/Update Base Meeting Point
            $mp = MeetingPoint::updateOrCreate(
                ['sort_order' => $pointData['sort_order']],
                [
                    'map_url'     => $pointData['map_url'],
                    'pickup_time' => $pointData['pickup_time'],
                    'is_active'   => $pointData['is_active'],
                ]
            );

            // 2. Create/Update 'es' Translation (Source)
            $mp->translations()->updateOrCreate(
                ['locale' => 'es'],
                [
                    'name'         => $pointData['name'],
                    'description'  => $pointData['description'],
                    'instructions' => $pointData['instructions'],
                ]
            );

            // 3. Generate Translations for other locales
            foreach ($targetLocales as $locale) {
                // If translation already exists and we don't want to overwrite, we could check here.
                // But for a seeder, usually we want to ensure data is present.
                // To avoid re-translating every time (expensive/slow), we could check if it exists nicely.
                // For now, I will force update as requested to "arreglar" (fix/ensure) it.

                try {
                    $this->command->info("   Translating to {$locale}: {$pointData['name']}...");

                    $translatedName = $translator->translate($pointData['name'], $locale);
                    $translatedDesc = $translator->translate($pointData['description'], $locale);
                    $translatedInstr = $translator->translate($pointData['instructions'], $locale);

                    $mp->translations()->updateOrCreate(
                        ['locale' => $locale],
                        [
                            'name'         => $translatedName,
                            'description'  => $translatedDesc,
                            'instructions' => $translatedInstr,
                        ]
                    );
                } catch (\Throwable $e) {
                    $this->command->warn("   ⚠️ Failed to translate to {$locale}: " . $e->getMessage());
                }
            }
        }

        $this->command->info('✅ Meeting points seeded and translated successfully');
    }
}
