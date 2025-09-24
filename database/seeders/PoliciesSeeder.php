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
             * 1) TÉRMINOS Y CONDICIONES (Categoría + Secciones)
             */
            $terms = Policy::updateOrCreate(
                ['name' => 'Términos y Condiciones'],
                [
                    'is_active'      => true,
                    'effective_from' => $today,
                    'effective_to'   => null,
                ]
            );

            $termsDescription = <<<'TXT'
Bienvenidos a Green Vacations Costa Rica
Estos términos y condiciones describen las reglas y regulaciones para el uso de Green Vacations Costa Rica, ubicado en greenvacationscr.com.

Al acceder a este sitio web asumimos que acepta estos términos y condiciones. No continúe usando greenvacationscr.com si no está de acuerdo con todos los términos y condiciones establecidos en esta página.

La siguiente terminología se aplica a estos Términos y condiciones, Declaración de privacidad y Aviso de exención de responsabilidad y todos los Acuerdos: «Cliente», «Usted» y «Su» se refiere a usted, la persona que inicia sesión en este sitio web y cumple con los términos y condiciones de Green Vacations Costa Rica. «La Compañía», «Nosotros mismos», «Nosotros», «Nuestro» y «Nosotros», se refiere a nuestra Compañía. «Parte», «Partes» o «Nosotros», se refiere tanto al Cliente como a nosotros mismos. Todos los términos se refieren a la oferta, aceptación y consideración del pago necesario para llevar a cabo el proceso de nuestra asistencia al Cliente de la manera más adecuada con el propósito expreso de satisfacer las necesidades del Cliente con respecto a la prestación de los servicios indicados por la Compañía, de conformidad con y sujeto a la legislación vigente de los Países Bajos. Cualquier uso de la terminología anterior u otras palabras en singular, plural, mayúsculas y/o él/ella o ellos, se consideran intercambiables y, por lo tanto, se refieren a las mismas.
TXT;

            PolicyTranslation::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'locale' => 'es'],
                [
                    'name'    => 'Términos y Condiciones',
                    'content' => $termsDescription,
                ]
            );

            // Sección: Cookies
            $cookies = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'cookies'], // 'name' actúa como key/slug
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

            // Sección: Licencia
            $license = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'licencia'],
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

            // Sección: Comentarios
            $comments = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'comentarios'],
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

            // Sección: Hipervínculos a nuestro contenido
            $links = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'hipervinculos'],
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

            // Sección: Marcos flotantes
            $frames = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'marcos'],
                ['sort_order' => 5, 'is_active' => true]
            );

            $framesContent = <<<'TXT'
Sin aprobación previa y permiso por escrito, no puede crear marcos alrededor de nuestras páginas web que alteren de ninguna manera la presentación visual o la apariencia de nuestro sitio web.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $frames->section_id, 'locale' => 'es'],
                ['name' => 'Marcos flotantes', 'content' => $framesContent]
            );

            // Sección: Responsabilidad por el contenido
            $contentResp = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'responsabilidad_contenido'],
                ['sort_order' => 6, 'is_active' => true]
            );

            $contentRespContent = <<<'TXT'
No seremos responsables de ningún contenido que aparezca en su sitio web. Usted acepta protegernos y defendernos contra todos los reclamos que surjan en su sitio web. Ningún enlace debe aparecer en ningún sitio web que pueda interpretarse como calumnioso, obsceno o criminal, o que infrinja o promueva la violación de derechos de terceros.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $contentResp->section_id, 'locale' => 'es'],
                ['name' => 'Responsabilidad por el contenido', 'content' => $contentRespContent]
            );

            // Sección: Reserva de Derechos
            $rights = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'reserva_derechos'],
                ['sort_order' => 7, 'is_active' => true]
            );

            $rightsContent = <<<'TXT'
Nos reservamos el derecho de solicitarle que elimine todos los enlaces o cualquier enlace particular a nuestro sitio web. Usted aprueba eliminar inmediatamente todos los enlaces a nuestro sitio web a pedido. También nos reservamos el derecho de modificar estos términos y condiciones y su política de vinculación en cualquier momento. Al vincularse continuamente a nuestro sitio web, usted acepta estar sujeto y seguir estos términos y condiciones de vinculación.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $rights->section_id, 'locale' => 'es'],
                ['name' => 'Reserva de Derechos', 'content' => $rightsContent]
            );

            // Sección: Eliminación de enlaces
            $removeLinks = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'eliminacion_enlaces'],
                ['sort_order' => 8, 'is_active' => true]
            );

            $removeLinksContent = <<<'TXT'
Si encuentra algún enlace en nuestro sitio web que sea ofensivo por cualquier motivo, puede contactarnos e informarnos en cualquier momento. Consideraremos solicitudes para eliminar enlaces, pero no estamos obligados a hacerlo ni a responderle directamente. No aseguramos que la información en este sitio web sea correcta, ni garantizamos su integridad o exactitud; ni prometemos que el sitio web permanezca disponible o que el material se mantenga actualizado.
TXT;

            PolicySectionTranslation::updateOrCreate(
                ['section_id' => $removeLinks->section_id, 'locale' => 'es'],
                ['name' => 'Eliminación de enlaces de nuestro sitio web', 'content' => $removeLinksContent]
            );

            // Sección: Descargo de responsabilidad
            $disclaimer = PolicySection::updateOrCreate(
                ['policy_id' => $terms->policy_id, 'name' => 'descargo'],
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
                ['name' => 'Política de Cancelación'],
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
                    'content' => "• Cancelaciones con 24 horas o más de antelación: reembolso completo.\n• Con menos de 24 horas o no presentación: no reembolsable.\n• Cambios de fecha sujetos a disponibilidad.",
                ]
            );

            /**
             * 3) REEMBOLSOS
             */
            $refund = Policy::updateOrCreate(
                ['name' => 'Política de Reembolsos'],
                [
                    'is_active'      => true,
                    'effective_from' => $today,
                    'effective_to'   => null,
                ]
            );

            PolicyTranslation::updateOrCreate(
                ['policy_id' => $refund->policy_id, 'locale' => 'es'],
                [
                    'name'    => 'Política de Reembolsos',
                    'content' => "• Los reembolsos se procesan al método de pago original.\n• El tiempo de acreditación depende del banco/emisor.\n• Cargos de terceros no siempre son reembolsables.\n• Podemos solicitar documentación adicional para validar el reembolso.",
                ]
            );

            /**
             * 4) PRIVACIDAD
             */
            $privacy = Policy::updateOrCreate(
                ['name' => 'Política de Privacidad'],
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
                    'content' => "• Tratamos datos personales conforme a la normativa aplicable.\n• Usamos la información para gestionar reservas y comunicación.\n• Puedes ejercer derechos de acceso, rectificación y supresión.\n• Compartimos datos con terceros solo cuando es necesario para la operación.",
                ]
            );
        });
    }
}
