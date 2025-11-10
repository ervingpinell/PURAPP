<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Policy;
use App\Models\PolicyTranslation;
use App\Models\PolicySection;
use App\Models\PolicySectionTranslation;

class PoliciesSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $today = Carbon::today()->toDateString();

            /**
             * 1) TERMS & CONDITIONS (Categoría + Secciones)
             * - policies: usa slug (en inglés, kebab-case)
             * - policy_sections: NO usa slug, se identifica por name (clave interna)
             * - name/content visibles quedan en *translations*
             */
            $terms = Policy::updateOrCreate(
                ['slug' => 'terms-and-conditions'],
                [
                    'is_active'      => true,
                    'effective_from' => $today,
                    'effective_to'   => null,
                ]
            );

            $termsDescription = <<<'TXT'
Bienvenidos a Green Vacations Costa Rica

Estos términos y condiciones describen las reglas y regulaciones para el uso del sitio web de Green Vacations Costa Rica, disponible en greenvacationscr.com.

Al acceder a este sitio web, asumimos que usted acepta estos términos y condiciones. No continúe utilizando greenvacationscr.com si no está de acuerdo con todos los términos y condiciones establecidos en esta página.

La siguiente terminología se aplica a estos Términos y Condiciones, a la Política de Privacidad y al Aviso de Exención de Responsabilidad: “Cliente”, “Usted” y “Su” se refieren a la persona que accede a este sitio web y que acepta los términos y condiciones de Green Vacations Costa Rica. “La Compañía”, “Nosotros”, “Nuestro” y “Nosotros mismos” se refieren a Green Vacations Costa Rica, empresa costarricense dedicada a la operación turística y venta de tours guiados en Costa Rica.

El término “Parte” o “Partes” hace referencia tanto al Cliente como a la Compañía. Todos los términos se refieren a la oferta, aceptación y consideración del pago necesario para llevar a cabo el proceso de prestación de nuestros servicios turísticos al Cliente de la manera más adecuada, con el propósito de satisfacer sus necesidades de viaje, conforme a la legislación vigente de Costa Rica.

Cualquier uso de la terminología anterior u otras palabras en singular, plural, mayúsculas y/o género distinto, se entenderán como intercambiables y, por lo tanto, se refieren al mismo concepto.
TXT;

            PolicyTranslation::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'locale' => 'es'],
                [
                    'name'    => 'Términos y Condiciones',
                    'content' => $termsDescription,
                ]
            );

            // ==== Secciones (identificadas por name "clave interna") ====

            // Cookies
            $cookies = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'cookies'],
                ['sort_order' => 1, 'is_active' => true]
            );

            $cookiesContent = <<<'TXT'
Empleamos el uso de cookies. Al acceder a greenvacationscr.com, usted aceptó usar cookies de acuerdo con la Política de Privacidad de Green Vacations Costa Rica.
La mayoría de los sitios web interactivos utilizan cookies para permitirnos recuperar los detalles del usuario para cada visita. Nuestro sitio web utiliza cookies para habilitar la funcionalidad de ciertas áreas para que sea más fácil para las personas que visitan nuestro sitio web. Algunos de nuestros socios afiliados/publicitarios también pueden usar cookies.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $cookies->section_id, 'locale' => 'es'],
                ['name' => 'Cookies', 'content' => $cookiesContent]
            );

            // License
            $license = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'license'],
                ['sort_order' => 2, 'is_active' => true]
            );

            $licenseContent = <<<'TXT'
A menos que se indique lo contrario, Green Vacations Costa Rica y/o sus licenciantes poseen los derechos de propiedad intelectual de todo el material en greenvacationscr.com. Todos los derechos de propiedad intelectual están reservados. Puede acceder a este sitio web para su uso personal sujeto a las restricciones establecidas en estos términos y condiciones.

No debes:
• Volver a publicar material de greenvacationscr.com
• Vender, alquilar o sublicenciar material de greenvacationscr.com
• Reproducir, duplicar o copiar material de greenvacationscr.com
• Redistribuir contenido de greenvacationscr.com

Este Acuerdo comenzará en la fecha del mismo.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $license->section_id, 'locale' => 'es'],
                ['name' => 'Licencia', 'content' => $licenseContent]
            );

            // Comments
            $comments = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'comments'],
                ['sort_order' => 3, 'is_active' => true]
            );

            $commentsContent = <<<'TXT'
Partes de este sitio web ofrecen una oportunidad para que los usuarios publiquen e intercambien opiniones e información en ciertas áreas del sitio web. Green Vacations Costa Rica no filtra, edita, publica o revisa los Comentarios antes de su presencia en el sitio web. Los comentarios no reflejan los puntos de vista y opiniones de Green Vacations Costa Rica, sus agentes y/o afiliados. En la medida en que lo permitan las leyes aplicables, Green Vacations Costa Rica no será responsable de los Comentarios ni de ninguna responsabilidad, daño o gasto causado y/o sufrido como resultado de cualquier uso y/o publicación y/o apariencia de los Comentarios en este sitio web.

Green Vacations Costa Rica se reserva el derecho de monitorear todos los Comentarios y eliminar cualquier Comentario que pueda considerarse inapropiado, ofensivo o que infrinja estos Términos y Condiciones.

Usted garantiza y declara que:
• Tiene derecho a publicar los Comentarios en nuestro sitio web y tiene todas las licencias y consentimientos necesarios para hacerlo.
• Los Comentarios no invaden ningún derecho de propiedad intelectual, incluidos, entre otros, derechos de autor, patentes o marcas comerciales de terceros.
• Los Comentarios no contienen ningún material difamatorio, calumnioso, ofensivo, indecente o ilegal que sea una invasión de la privacidad.
• Los Comentarios no se utilizarán para solicitar o promover negocios o costumbres o presentar actividades comerciales o actividades ilegales.

Por la presente, otorga a Green Vacations Costa Rica una licencia no exclusiva para usar, reproducir, editar y autorizar a otros a usar, reproducir y editar cualquiera de sus Comentarios en cualquiera y todas las formas, formatos o medios.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $comments->section_id, 'locale' => 'es'],
                ['name' => 'Comentarios', 'content' => $commentsContent]
            );

            // Hyperlinks to our content
            $links = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'hyperlinks-to-our-content'],
                ['sort_order' => 4, 'is_active' => true]
            );

            $linksContent = <<<'TXT'
Las siguientes organizaciones pueden vincular a nuestro sitio web sin aprobación previa por escrito: agencias gubernamentales; motores de búsqueda; organizaciones de noticias; distribuidores de directorios en línea en la misma forma en que enlazan a otros sitios; y empresas acreditadas en todo el sistema (excepto organizaciones sin fines de lucro, centros comerciales de caridad y grupos de recaudación de fondos de caridad).
Estas organizaciones pueden vincularse a nuestra página de inicio, publicaciones u otra información siempre que el enlace: (a) no sea engañoso; (b) no implique falsamente patrocinio, respaldo o aprobación; y (c) encaje en el contexto del sitio de la parte vinculada.

Podemos considerar y aprobar otras solicitudes de enlace de: fuentes de información comercial/consumo, sitios de comunidad, asociaciones u otros grupos benéficos, distribuidores de directorios en línea, portales de internet, firmas profesionales, instituciones educativas y asociaciones comerciales.
Aprobaremos si: (a) el enlace no nos hace ver desfavorablemente; (b) la organización no tiene registros negativos con nosotros; (c) el beneficio de la visibilidad compensa la ausencia de Green Vacations Costa Rica; y (d) el enlace está en el contexto de información general de recursos.

Las organizaciones aprobadas pueden enlazar usando: nuestro nombre corporativo; el URL al que se enlaza; o cualquier descripción razonable de nuestro sitio web. No se permite el uso del logotipo u otra obra de arte sin acuerdo de licencia.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $links->section_id, 'locale' => 'es'],
                ['name' => 'Hipervínculos a nuestro contenido', 'content' => $linksContent]
            );

            // Frames
            $frames = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'frames'],
                ['sort_order' => 5, 'is_active' => true]
            );

            $framesContent = <<<'TXT'
Sin aprobación previa y permiso por escrito, no puede crear marcos alrededor de nuestras páginas web que alteren de ninguna manera la presentación visual o la apariencia de nuestro sitio web.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $frames->section_id, 'locale' => 'es'],
                ['name' => 'Marcos flotantes', 'content' => $framesContent]
            );

            // Content liability
            $contentResp = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'content-liability'],
                ['sort_order' => 6, 'is_active' => true]
            );

            $contentRespContent = <<<'TXT'
No seremos responsables de ningún contenido que aparezca en su sitio web. Usted acepta protegernos y defendernos contra todos los reclamos que surjan en su sitio web. Ningún enlace debe aparecer en ningún sitio web que pueda interpretarse como calumnioso, obsceno o criminal, o que infrinja o promueva la violación de derechos de terceros.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $contentResp->section_id, 'locale' => 'es'],
                ['name' => 'Responsabilidad por el contenido', 'content' => $contentRespContent]
            );

            // Reservation of rights
            $rights = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'reservation-of-rights'],
                ['sort_order' => 7, 'is_active' => true]
            );

            $rightsContent = <<<'TXT'
Nos reservamos el derecho de solicitarle que elimine todos los enlaces o cualquier enlace particular a nuestro sitio web. Usted aprueba eliminar inmediatamente todos los enlaces a nuestro sitio web a pedido. También nos reservamos el derecho de modificar estos términos y condiciones y su política de vinculación en cualquier momento. Al vincularse continuamente a nuestro sitio web, usted acepta estar sujeto y seguir estos términos y condiciones de vinculación.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $rights->section_id, 'locale' => 'es'],
                ['name' => 'Reserva de Derechos', 'content' => $rightsContent]
            );

            // Removal of links
            $removeLinks = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'removal-of-links'],
                ['sort_order' => 8, 'is_active' => true]
            );

            $removeLinksContent = <<<'TXT'
Si encuentra algún enlace en nuestro sitio web que sea ofensivo por cualquier motivo, puede contactarnos e informarnos en cualquier momento. Consideraremos solicitudes para eliminar enlaces, pero no estamos obligados a hacerlo ni a responderle directamente. No aseguramos que la información en este sitio web sea correcta, ni garantizamos su integridad o exactitud; ni prometemos que el sitio web permanezca disponible o que el material se mantenga actualizado.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $removeLinks->section_id, 'locale' => 'es'],
                ['name' => 'Eliminación de enlaces de nuestro sitio web', 'content' => $removeLinksContent]
            );

            // Disclaimer
            $disclaimer = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'disclaimer'],
                ['sort_order' => 9, 'is_active' => true]
            );

            $disclaimerContent = <<<'TXT'
En la máxima medida permitida por la ley aplicable, excluimos todas las representaciones, garantías y condiciones relacionadas con nuestro sitio web y el uso de este sitio web. Nada en este descargo de responsabilidad: (i) limita o excluye nuestra o su responsabilidad por muerte o lesiones personales; (ii) limita o excluye nuestra o su responsabilidad por fraude o tergiversación fraudulenta; (iii) limita cualquiera de nuestras responsabilidades o las suyas de cualquier manera no permitida por la ley; o (iv) excluye cualquiera de nuestras o sus responsabilidades que no puedan ser excluidas bajo la ley aplicable.
Las limitaciones y prohibiciones de responsabilidad establecidas en este documento rigen todas las responsabilidades que surjan por contrato, agravio o incumplimiento del deber legal. Siempre que el sitio web y la información/servicios se proporcionen de forma gratuita, no seremos responsables de ninguna pérdida o daño de ningún tipo.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $disclaimer->section_id, 'locale' => 'es'],
                ['name' => 'Descargo de responsabilidad', 'content' => $disclaimerContent]
            );

            /**
             * 2) CANCELACIÓN
             */
            $cancel = Policy::updateOrCreate(
                ['slug' => 'cancellation-policies'],
                [
                    'is_active'      => true,
                    'effective_from' => $today,
                    'effective_to'   => null,
                ]
            );

            PolicyTranslation::updateOrCreate(
                ['policy_id' => $cancel->policy_id, 'locale' => 'es'],
                [
                    'name'    => 'Política de Cancelación',
                    'content' => "• Las cancelaciones realizadas con 24 horas o más de antelación recibirán un reembolso del 100%.\n• Las cancelaciones efectuadas con menos de 24 horas de antelación, o en caso de no presentarse al tour, no son reembolsables (0%).\n• Las reprogramaciones podrán gestionarse con un mínimo de 12 horas antes del inicio del tour y estarán sujetas a disponibilidad.\n• Los reembolsos se procesarán únicamente a la misma tarjeta con la cual se realizó la compra original.\n• Para realizar cancelaciones, reprogramaciones o consultas, puede contactarnos mediante correo electrónico a info@greenvacationscr.com o vía telefónica/WhatsApp al +506 2470-1471.",
                ]
            );

            /**
             * 3) REEMBOLSOS
             */
            $refund = Policy::updateOrCreate(
                ['slug' => 'refund-policies'],
                [
                    'is_active'      => true,
                    'effective_from' => $today,
                    'effective_to'   => null,
                ]
            );

            PolicyTranslation::updateOrCreate(
                ['policy_id' => $refund->policy_id, 'locale' => 'es'],
                [
                    'name'    => 'Política de Devoluciones y Reembolsos',
                    'content' => "• Las devoluciones de dinero se realizan únicamente cuando el servicio no pueda ser brindado por causas atribuibles a Green Vacations Costa Rica (por ejemplo, cancelación del tour por condiciones climáticas extremas o causas operativas justificadas).\n• No se realizarán devoluciones si el cliente no se presenta al tour o cancela fuera del plazo establecido en nuestras políticas de cancelación.\n• Los reembolsos se procesarán únicamente a la misma tarjeta con la cual se efectuó la compra original.\n• El tiempo de acreditación del reembolso dependerá del banco o emisor de la tarjeta utilizada.\n• Los cargos aplicados por terceros (como plataformas de pago o entidades financieras) podrían no ser reembolsables.\n• Para solicitar una devolución o aclarar dudas sobre su reembolso, puede contactarnos a través del correo info@greenvacationscr.com o mediante teléfono/WhatsApp al +506 2470-1471.",
                ]
            );

            /**
             * 4) PRIVACIDAD
             */
            $privacy = Policy::updateOrCreate(
                ['slug' => 'privacy-policy'],
                [
                    'is_active'      => true,
                    'effective_from' => $today,
                    'effective_to'   => null,
                ]
            );

            PolicyTranslation::updateOrCreate(
                ['policy_id' => $privacy->policy_id, 'locale' => 'es'],
                [
                    'name'    => 'Política de Privacidad',
                    'content' => "• Tratamos datos personales conforme a la normativa aplicable.\n• Usamos la información para gestionar reservas y comunicación.\n• Puedes ejercer derechos de acceso, rectificación y supresión.\n• La información no es vendida ni compartida con terceros.",
                ]
            );

            /**
             * 5) GARANTÍAS
             */
            $warranty = Policy::updateOrCreate(
                ['slug' => 'warranty-policies'],
                [
                    'is_active'      => true,
                    'effective_from' => $today,
                    'effective_to'   => null,
                ]
            );

            $warrantyContent = <<<'TXT'
• Green Vacations Costa Rica garantiza la correcta prestación de los servicios turísticos contratados, asegurando el cumplimiento de los estándares de calidad y seguridad ofrecidos en cada tour.
• Si el cliente considera que el servicio recibido no corresponde con lo contratado, podrá presentar una solicitud formal de revisión o garantía dentro de un plazo máximo de 7 días naturales posteriores a la fecha del tour.
• Esta garantía aplica únicamente a servicios operados directamente por Green Vacations Costa Rica y no cubre servicios de terceros, como transporte externo, hospedaje, alimentación o actividades subcontratadas.
• En caso de validarse una inconformidad, Green Vacations Costa Rica podrá ofrecer una compensación, reprogramación del tour o reembolso parcial, según corresponda.
• Para gestionar una solicitud de garantía o enviar documentación de respaldo, puede contactarnos mediante correo electrónico a info@greenvacationscr.com o vía telefónica/WhatsApp al +506 2470-1471.
TXT;

            PolicyTranslation::updateOrCreate(
                ['policy_id' => $warranty->policy_id, 'locale' => 'es'],
                [
                    'name'    => 'Política de Garantías',
                    'content' => $warrantyContent,
                ]
            );
        });

        $this->command->info('✅ Policies seeded successfully (policy slugs only; section names as internal keys; translations saved).');
    }
}
