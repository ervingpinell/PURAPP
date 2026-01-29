<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\Policy;
use App\Models\PolicyTranslation;
use App\Models\PolicySection;
use App\Models\PolicySectionTranslation;
use App\Services\Contracts\TranslatorInterface;

class PoliciesSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Limpia tablas (en orden seguro)
        $this->wipePolicyTables();

        DB::transaction(function () {
            $today = Carbon::today()->toDateString();

            // === Traductor (opcional). Si no existe, hace fallback a ES.
            $translator = null;
            try {
                $translator = app()->make(TranslatorInterface::class);
            } catch (\Throwable $e) {
                // sin traductor => se copian textos ES
            }

            // Normaliza locales si tu modelo lo soporta
            $canon = function (string $lang): string {
                if (method_exists(Policy::class, 'canonicalLocale')) {
                    return Policy::canonicalLocale($lang);
                }
                return in_array($lang, ['es', 'en', 'fr', 'de', 'pt'], true) ? $lang : 'es';
            };

            // Traducción masiva con fallback, preservando saltos de línea y viñetas
            $translateAll = function (string $text) use ($translator): array {
                $out = ['en' => '', 'fr' => '', 'de' => '', 'pt' => ''];
                
                // Dividir por líneas para asegurar que bullets no se mezclen
                $lines = explode("\n", $text);
                
                // Arrays temporales para reconstruir cada idioma
                $buffer = [
                    'en' => [],
                    'fr' => [],
                    'de' => [],
                    'pt' => [],
                ];

                foreach ($lines as $line) {
                    $trimmed = trim($line);
                    if ($trimmed === '') {
                        // Línea vacía: agregar vacía a todos
                        foreach ($buffer as $k => $v) {
                            $buffer[$k][] = '';
                        }
                        continue;
                    }

                    // Traducir la línea
                    if ($translator) {
                        try {
                            $res = (array) ($translator->translateAll($line) ?? []);
                            // Si falla algo, fallback al original
                            foreach (array_keys($buffer) as $lang) {
                                $trans = $res[$lang] ?? null;
                                $buffer[$lang][] = $trans ?: $line;
                            }
                        } catch (\Throwable $e) {
                            // Fallback total para esta línea
                            foreach (array_keys($buffer) as $lang) {
                                $buffer[$lang][] = $line;
                            }
                        }
                    } else {
                        // Sin traductor
                        foreach (array_keys($buffer) as $lang) {
                            $buffer[$lang][] = $line;
                        }
                    }
                }

                // Reconstruir
                foreach ($buffer as $lang => $arr) {
                    $out[$lang] = implode("\n", $arr);
                }
                
                return $out;
            };

            // === Helpers de traducciones para policies
            $savePolicyTranslations = function (int $policyId, string $nameEs, string $contentEs) use ($translateAll, $canon) {
                // ES
                PolicyTranslation::updateOrCreate(
                    ['policy_id' => $policyId, 'locale' => 'es'],
                    ['name' => $nameEs, 'content' => $contentEs]
                );
                // Otros idiomas
                $nameAll = $translateAll($nameEs);
                $contAll = $translateAll($contentEs); // Ahora preserva estructura
                foreach (['en', 'fr', 'de', 'pt'] as $lang) {
                    $locale = $canon($lang);
                    PolicyTranslation::updateOrCreate(
                        ['policy_id' => $policyId, 'locale' => $locale],
                        [
                            'name'    => $nameAll[$lang] ?? $nameEs,
                            'content' => $contAll[$lang] ?? $contentEs,
                        ]
                    );
                }
            };
            
            // ... (rest of helper functions) ...

            // === Detección del nombre del FK real
            $sectionFkCol = Schema::hasColumn('policy_section_translations', 'policy_section_id')
                ? 'policy_section_id'
                : 'section_id';

            $upsertSectionWithTranslations = function (
                int $policyId,
                string $sectionKey,
                int $sort,
                string $nameEs,
                string $contentEs
            ) use ($sectionFkCol, $translateAll, $canon) {
                // 1) Base
                $section = PolicySection::firstOrCreate(
                    ['policy_id' => $policyId, 'sort_order' => $sort],
                    ['is_active' => true]
                );
                $sectionId = $section->policy_section_id ?? $section->section_id ?? $section->getKey();

                // 2) Traducción ES
                PolicySectionTranslation::updateOrCreate(
                    [$sectionFkCol => $sectionId, 'locale' => 'es'],
                    ['name' => $nameEs, 'content' => $contentEs]
                );

                // 3) Otras traducciones
                $nameAll = $translateAll($nameEs);
                $contAll = $translateAll($contentEs); // Preserva estructura
                foreach (['en', 'fr', 'de', 'pt'] as $lang) {
                    $loc = $canon($lang);
                    PolicySectionTranslation::updateOrCreate(
                        [$sectionFkCol => $sectionId, 'locale' => $loc],
                        ['name' => $nameAll[$lang] ?? $nameEs, 'content' => $contAll[$lang] ?? $contentEs]
                    );
                }
            };

            // =========================
            // 1) TÉRMINOS Y CONDICIONES (contenido omitido para brevedad en reemplazo, usar contexto anterior si necesario)
            // ... (se asume que no se toca el contenido de Terms aquí, solo se redefinen los helpers arriba)
            // ERROR: replace_file_content no puede "ignorar" el medio. Debo reemplazar el bloque exacto.
            // Dado que el bloque es grande, mejor uso multi_replace o targeteo específicamente la función.
            // Voy a usar replace_file_content SOLAMENTE en la función $translateAll y luego otro cambio para el slug.



            // =========================
            // 1) TÉRMINOS Y CONDICIONES
            // =========================
            $terms = Policy::updateOrCreate(
                ['slug' => 'terms-and-conditions'],
                [
                    'is_active'      => true,
                    'effective_from' => $today,
                    'effective_to'   => null,
                ]
            );

            $termsNameEs = 'Términos y Condiciones';
            $termsContentEs = <<<'TXT'
Bienvenidos a Green Vacations Costa Rica

Estos términos y condiciones describen las reglas y regulaciones para el uso del sitio web de Green Vacations Costa Rica, disponible en greenvacationscr.com.

Al acceder a este sitio web, asumimos que usted acepta estos términos y condiciones. No continúe utilizando greenvacationscr.com si no está de acuerdo con todos los términos y condiciones establecidos en esta página.

La siguiente terminología se aplica a estos Términos y Condiciones, a la Política de Privacidad y al Aviso de Exención de Responsabilidad: “Cliente”, “Usted” y “Su” se refieren a la persona que accede a este sitio web y que acepta los términos y condiciones de Green Vacations Costa Rica. “La Compañía”, “Nosotros”, “Nuestro” y “Nosotros mismos” se refieren a Green Vacations Costa Rica, empresa costarricense dedicada a la operación turística y venta de products guiados en Costa Rica.

El término “Parte” o “Partes” hace referencia tanto al Cliente como a la Compañía. Todos los términos se refieren a la oferta, aceptación y consideración del pago necesario para llevar a cabo el proceso de prestación de nuestros servicios turísticos al Cliente de la manera más adecuada, con el propósito de satisfacer sus necesidades de viaje, conforme a la legislación vigente de Costa Rica.

Cualquier uso de la terminología anterior u otras palabras en singular, plural, mayúsculas y/o género distinto, se entenderán como intercambiables y, por lo tanto, se refieren al mismo concepto.
TXT;

            $savePolicyTranslations($terms->policy_id, $termsNameEs, $termsContentEs);

            // Secciones (usar slug/clave + traducciones)
            $upsertSectionWithTranslations(
                $terms->policy_id,
                'cookies',
                1,
                'Cookies',
                <<<'TXT'
Empleamos el uso de cookies. Al acceder a greenvacationscr.com, usted aceptó usar cookies de acuerdo con la Política de Privacidad de Green Vacations Costa Rica.
La mayoría de los sitios web interactivos utilizan cookies para permitirnos recuperar los detalles del usuario para cada visita. Nuestro sitio web utiliza cookies para habilitar la funcionalidad de ciertas áreas para que sea más fácil para las personas que visitan nuestro sitio web. Algunos de nuestros socios afiliados/publicitarios también pueden usar cookies.
TXT
            );

            $upsertSectionWithTranslations(
                $terms->policy_id,
                'license',
                2,
                'Licencia',
                <<<'TXT'
A menos que se indique lo contrario, Green Vacations Costa Rica y/o sus licenciantes poseen los derechos de propiedad intelectual de todo el material en greenvacationscr.com. Todos los derechos de propiedad intelectual están reservados. Puede acceder a este sitio web para su uso personal sujeto a las restricciones establecidas en estos términos y condiciones.

No debes:
• Volver a publicar material de greenvacationscr.com
• Vender, alquilar o sublicenciar material de greenvacationscr.com
• Reproducir, duplicar o copiar material de greenvacationscr.com
• Redistribuir contenido de greenvacationscr.com

Este Acuerdo comenzará en la fecha del mismo.
TXT
            );

            $upsertSectionWithTranslations(
                $terms->policy_id,
                'comments',
                3,
                'Comentarios',
                <<<'TXT'
Partes de este sitio web ofrecen una oportunidad para que los usuarios publiquen e intercambien opiniones e información en ciertas áreas del sitio web. Green Vacations Costa Rica no filtra, edita, publica o revisa los Comentarios antes de su presencia en el sitio web. Los comentarios no reflejan los puntos de vista y opiniones de Green Vacations Costa Rica, sus agentes y/o afiliados. En la medida en que lo permitan las leyes aplicables, Green Vacations Costa Rica no será responsable de los Comentarios ni de ninguna responsabilidad, daño o gasto causado y/o sufrido como resultado de cualquier uso y/o publicación y/o apariencia de los Comentarios en este sitio web.

Green Vacations Costa Rica se reserva el derecho de monitorear todos los Comentarios y eliminar cualquier Comentario que pueda considerarse inapropiado, ofensivo o que infrinja estos Términos y Condiciones.

Usted garantiza y declara que:
• Tiene derecho a publicar los Comentarios en nuestro sitio web y tiene todas las licencias y consentimientos necesarios para hacerlo.
• Los Comentarios no invaden ningún derecho de propiedad intelectual, incluidos, entre otros, derechos de autor, patentes o marcas comerciales de terceros.
• Los Comentarios no contienen ningún material difamatorio, calumnioso, ofensivo, indecente o ilegal que sea una invasión de la privacidad.
• Los Comentarios no se utilizarán para solicitar o promover negocios o costumbres o presentar actividades comerciales o actividades ilegales.

Por la presente, otorga a Green Vacations Costa Rica una licencia no exclusiva para usar, reproducir, editar y autorizar a otros a usar, reproducir y editar cualquiera de sus Comentarios en cualquiera y todas las formas, formatos o medios.
TXT
            );

            $upsertSectionWithTranslations(
                $terms->policy_id,
                'hyperlinks-to-our-content',
                4,
                'Hipervínculos a nuestro contenido',
                <<<'TXT'
Las siguientes organizaciones pueden vincular a nuestro sitio web sin aprobación previa por escrito: agencias gubernamentales; motores de búsqueda; organizaciones de noticias; distribuidores de directorios en línea en la misma forma en que enlazan a otros sitios; y empresas acreditadas en todo el sistema (excepto organizaciones sin fines de lucro, centros comerciales de caridad y grupos de recaudación de fondos de caridad).
Estas organizaciones pueden vincularse a nuestra página de inicio, publicaciones u otra información siempre que el enlace: (a) no sea engañoso; (b) no implique falsamente patrocinio, respaldo o aprobación; y (c) encaje en el contexto del sitio de la parte vinculada.

Podemos considerar y aprobar otras solicitudes de enlace de: fuentes de información comercial/consumo, sitios de comunidad, asociaciones u otros grupos benéficos, distribuidores de directorios en línea, portales de internet, firmas profesionales, instituciones educativas y asociaciones comerciales.
Aprobaremos si: (a) el enlace no nos hace ver desfavorablemente; (b) la organización no tiene registros negativos con nosotros; (c) el beneficio de la visibilidad compensa la ausencia de Green Vacations Costa Rica; y (d) el enlace está en el contexto de información general de recursos.

Las organizaciones aprobadas pueden enlazar usando: nuestro nombre corporativo; el URL al que se enlaza; o cualquier descripción razonable de nuestro sitio web. No se permite el uso del logotipo u otra obra de arte sin acuerdo de licencia.
TXT
            );

            $upsertSectionWithTranslations(
                $terms->policy_id,
                'frames',
                5,
                'Marcos flotantes',
                'Sin aprobación previa y permiso por escrito, no puede crear marcos alrededor de nuestras páginas web que alteren de ninguna manera la presentación visual o la apariencia de nuestro sitio web.'
            );

            $upsertSectionWithTranslations(
                $terms->policy_id,
                'content-liability',
                6,
                'Responsabilidad por el contenido',
                'No seremos responsables de ningún contenido que aparezca en su sitio web. Usted acepta protegernos y defendernos contra todos los reclamos que surjan en su sitio web. Ningún enlace debe aparecer en ningún sitio web que pueda interpretarse como calumnioso, obsceno o criminal, o que infrinja o promueva la violación de derechos de terceros.'
            );

            $upsertSectionWithTranslations(
                $terms->policy_id,
                'reservation-of-rights',
                7,
                'Reserva de Derechos',
                'Nos reservamos el derecho de solicitarle que elimine todos los enlaces o cualquier enlace particular a nuestro sitio web. Usted aprueba eliminar inmediatamente todos los enlaces a nuestro sitio web a pedido. También nos reservamos el derecho de modificar estos términos y condiciones y su política de vinculación en cualquier momento. Al vincularse continuamente a nuestro sitio web, usted acepta estar sujeto y seguir estos términos y condiciones de vinculación.'
            );

            $upsertSectionWithTranslations(
                $terms->policy_id,
                'removal-of-links',
                8,
                'Eliminación de enlaces de nuestro sitio web',
                'Si encuentra algún enlace en nuestro sitio web que sea ofensivo por cualquier motivo, puede contactarnos e informarnos en cualquier momento. Consideraremos solicitudes para eliminar enlaces, pero no estamos obligados a hacerlo ni a responderle directamente. No aseguramos que la información en este sitio web sea correcta, ni garantizamos su integridad o exactitud; ni prometemos que el sitio web permanezca disponible o que el material se mantenga actualizado.'
            );

            $upsertSectionWithTranslations(
                $terms->policy_id,
                'disclaimer',
                9,
                'Descargo de responsabilidad',
                <<<'TXT'
En la máxima medida permitida por la ley aplicable, excluimos todas las representaciones, garantías y condiciones relacionadas con nuestro sitio web y el uso de este sitio web. Nada en este descargo de responsabilidad: (i) limita o excluye nuestra o su responsabilidad por muerte o lesiones personales; (ii) limita o excluye nuestra o su responsabilidad por fraude o tergiversación fraudulenta; (iii) limita cualquiera de nuestras responsabilidades o las suyas de cualquier manera no permitida por la ley; o (iv) excluye cualquiera de nuestras o sus responsabilidades que no puedan ser excluidas bajo la ley aplicable.
Las limitaciones y prohibiciones de responsabilidad establecidas en este documento rigen todas las responsabilidades que surjan por contrato, agravio o incumplimiento del deber legal. Siempre que el sitio web y la información/servicios se proporcionen de forma gratuita, no seremos responsables de ninguna pérdida o daño de ningún tipo.
TXT
            );

            // =========================
            // 2) CANCELACIÓN
            // =========================
            $cancel = Policy::updateOrCreate(
                ['slug' => 'cancellations-policies'],
                ['is_active' => true, 'effective_from' => $today, 'effective_to' => null]
            );
            $savePolicyTranslations(
                $cancel->policy_id,
                'Política de Cancelación',
                "• Las cancelaciones realizadas con 24 horas o más de antelación recibirán un reembolso del 100%.\n• Las cancelaciones efectuadas con menos de 24 horas de antelación, o en caso de no presentarse al product, no son reembolsables (0%).\n• Las reprogramaciones podrán gestionarse con un mínimo de 12 horas antes del inicio del product y estarán sujetas a disponibilidad.\n• Los reembolsos se procesarán únicamente a la misma tarjeta con la cual se realizó la compra original.\n• Para realizar cancelaciones, reprogramaciones o consultas, puede contactarnos mediante correo electrónico a info@greenvacationscr.com o vía telefónica/WhatsApp al +506 2470-1471."
            );

            // =========================
            // 3) REEMBOLSOS
            // =========================
            $refund = Policy::updateOrCreate(
                ['slug' => 'refund-policies'],
                ['is_active' => true, 'effective_from' => $today, 'effective_to' => null]
            );
            $savePolicyTranslations(
                $refund->policy_id,
                'Política de Devoluciones y Reembolsos',
                "• Las devoluciones de dinero se realizan únicamente cuando el servicio no pueda ser brindado por causas atribuibles a Green Vacations Costa Rica (por ejemplo, cancelación del product por condiciones climáticas extremas o causas operativas justificadas).\n• No se realizarán devoluciones si el cliente no se presenta al product o cancela fuera del plazo establecido en nuestras políticas de cancelación.\n• Los reembolsos se procesarán únicamente a la misma tarjeta con la cual se efectuó la compra original.\n• El tiempo de acreditación del reembolso dependerá del banco o emisor de la tarjeta utilizada.\n• Los cargos aplicados por terceros (como plataformas de pago o entidades financieras) podrían no ser reembolsables.\n• Para solicitar una devolución o aclarar dudas sobre su reembolso, puede contactarnos a través del correo info@greenvacationscr.com o mediante teléfono/WhatsApp al +506 2470-1471."
            );

            // =========================
            // 4) PRIVACIDAD
            // =========================
            $privacy = Policy::updateOrCreate(
                ['slug' => 'privacy-policy'],
                ['is_active' => true, 'effective_from' => $today, 'effective_to' => null]
            );
            $savePolicyTranslations(
                $privacy->policy_id,
                'Política de Privacidad',
                "• Tratamos datos personales conforme a la normativa aplicable.\n• Usamos la información para gestionar reservas y comunicación.\n• Puedes ejercer derechos de acceso, rectificación y supresión.\n• La información no es vendida ni compartida con terceros."
            );

            // =========================
            // 5) GARANTÍAS
            // =========================
            $warranty = Policy::updateOrCreate(
                ['slug' => 'warranty-policies'],
                ['is_active' => true, 'effective_from' => $today, 'effective_to' => null]
            );
            $savePolicyTranslations(
                $warranty->policy_id,
                'Política de Garantías',
                <<<'TXT'
• Green Vacations Costa Rica garantiza la correcta prestación de los servicios turísticos contratados, asegurando el cumplimiento de los estándares de calidad y seguridad ofrecidos en cada product.
• Si el cliente considera que el servicio recibido no corresponde con lo contratado, podrá presentar una solicitud formal de revisión o garantía dentro de un plazo máximo de 7 días naturales posteriores a la fecha del product.
• Esta garantía aplica únicamente a servicios operados directamente por Green Vacations Costa Rica y no cubre servicios de terceros, como transporte externo, hospedaje, alimentación o actividades subcontratadas.
• En caso de validarse una inconformidad, Green Vacations Costa Rica podrá ofrecer una compensación, reprogramación del product o reembolso parcial, según corresponda.
• Para gestionar una solicitud de garantía o enviar documentación de respaldo, puede contactarnos mediante correo electrónico a info@greenvacationscr.com o vía telefónica/WhatsApp al +506 2470-1471.
TXT
            );
        });

        $this->command?->info('✅ PoliciesSeederTranslateWipe: wiped & seeded (es/en/fr/de/pt).');
    }

    /**
     * Vacía policies y sus tablas relacionadas en orden seguro.
     */
    protected function wipePolicyTables(): void
    {
        $driver = DB::getDriverName();

        $tblPolicies        = 'policies';
        $tblPolTranslations = 'policies_translations';
        $tblSections        = 'policy_sections';
        $tblSecTranslations = 'policy_section_translations'; // <— nombre correcto

        $tablesInOrder = [
            $tblSecTranslations,   // depende de policy_sections
            $tblPolTranslations,   // depende de policies
            $tblSections,          // depende de policies
            $tblPolicies,
        ];

        if ($driver === 'pgsql') {
            foreach ($tablesInOrder as $t) {
                if (!Schema::hasTable($t)) continue;
                DB::statement('TRUNCATE TABLE "'.$t.'" RESTART IDENTITY CASCADE');
            }
        } else {
            Schema::disableForeignKeyConstraints();
            foreach ($tablesInOrder as $t) {
                if (!Schema::hasTable($t)) continue;
                try {
                    DB::table($t)->truncate();
                } catch (\Throwable $e) {
                    DB::statement("DELETE FROM {$t}");
                    try { DB::statement("DELETE FROM sqlite_sequence WHERE name = '{$t}'"); } catch (\Throwable $e2) {}
                }
            }
            Schema::enableForeignKeyConstraints();
        }
    }
}
