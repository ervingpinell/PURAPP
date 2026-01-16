<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 🗑️ LIMPIEZA DE PERMISOS LEGADO (Manage)
        // Eliminamos explícitamente los permisos 'manage-' que ya no se usan para limpiar la BD
        $legacyPermissions = [
            'manage-users',
            'manage-reviews',
            'manage-review-providers',
            'manage-review-requests',
            'manage-settings',
            'manage-taxes',
            'manage-faqs',
            'manage-translations',
            'manage-policies',
            'manage-customer-categories',
            'manage-payments',
            'manage-tour-types',
            'manage-amenities',
            'manage-tour-pricing',
            'manage-tour-availability',
            'manage-tour-images',
            'assign-tour-schedules', // Unified with publish assignments,
            'publish-tour-schedule-assignments', // Renamed
            'edit-tour-type-covers', // Renamed
            'tours.manage',
            
            // Old delete permissions consolidated into soft/hard delete
            'delete-bookings',
            'delete-reviews',
            'delete-review-providers',
            'delete-taxes',
            'delete-faqs',
            'delete-customer_categories',
            'delete-tour_languages',
            'delete-amenities',
            'delete-tour_types',
            'delete-itineraries',
            'delete-schedules',
            'delete-meeting_points',
            'delete-policies',
            'delete-policy-sections',
        ];

        Permission::whereIn('name', $legacyPermissions)->delete();

        // Definir todos los permisos del sistema
        $permissions = [
            // Acceso general
            'access-admin' => 'Acceder al panel de administración',

            // Usuarios
            'view-users' => 'Ver usuarios',
            'create-users' => 'Crear usuarios',
            'edit-users' => 'Editar usuarios',
            'soft-delete-users' => 'Eliminar usuarios (papelera)',
            'restore-users' => 'Restaurar usuarios eliminados',
            'hard-delete-users' => 'Eliminar usuarios permanentemente',

            // Roles (solo super admin y admin)
            'view-roles' => 'Ver roles',
            'create-roles' => 'Crear roles',
            'edit-roles' => 'Editar roles',
            'delete-roles' => 'Eliminar roles',
            'publish-roles' => 'Activar/Desactivar roles',
            'assign-roles' => 'Asignar roles a usuarios',

            // Tours
            'view-tours' => 'Ver tours',
            'create-tours' => 'Crear tours',
            'edit-tours' => 'Editar tours',
            'soft-delete-tours' => 'Eliminar tours (papelera)',
            'restore-tours' => 'Restaurar tours eliminados',
            'hard-delete-tours' => 'Eliminar tours permanentemente',
            'publish-tours' => 'Publicar/despublicar tours',
            'reorder-tours' => 'Ordenar tours',
            'view-tour-pricing' => 'Ver precios de tours',
            'edit-tour-pricing' => 'Editar precios de tours',
            'view-tour-availability' => 'Ver disponibilidad de tours',
            'edit-tour-availability' => 'Editar disponibilidad de tours',
            'view-tour-images' => 'Ver imágenes de tours',
            'create-tour-images' => 'Subir imágenes de tours',
            'delete-tour-images' => 'Eliminar imágenes de tours',
            'edit_cover-tour-images' => 'Editar covers de tipos de tour',

            // Reservas (Bookings)
            'view-bookings' => 'Ver reservas',
            'create-bookings' => 'Crear reservas',
            'edit-bookings' => 'Editar reservas',
            'cancel-bookings' => 'Cancelar reservas',
            'soft-delete-bookings' => 'Eliminar reservas (papelera)',
            'restore-bookings' => 'Restaurar reservas eliminadas',
            'hard-delete-bookings' => 'Eliminar reservas permanentemente',

            // Reseñas (Reviews)
            'view-reviews' => 'Ver reseñas',
            'moderate-reviews' => 'Moderar reseñas',
            'reply-reviews' => 'Responder reseñas',
            'soft-delete-reviews' => 'Eliminar reseñas (papelera)',
            'restore-reviews' => 'Restaurar reseñas eliminadas',
            'hard-delete-reviews' => 'Eliminar reseñas permanentemente',

            // Proveedores de reseñas
            'view-review-providers' => 'Ver proveedores de reseñas',
            'create-review-providers' => 'Crear proveedores de reseñas',
            'edit-review-providers' => 'Editar proveedores de reseñas',
            'publish-review-providers' => 'Publicar proveedores de reseñas',
            'soft-delete-review-providers' => 'Eliminar proveedores (papelera)',
            'hard-delete-review-providers' => 'Eliminar proveedores permanentemente',

            // Solicitudes de reseñas
            'view-review-requests' => 'Ver solicitudes de reseñas',
            'create-review-requests' => 'Crear solicitudes de reseñas',
            'edit-review-requests' => 'Editar solicitudes de reseñas',
            'delete-review-requests' => 'Eliminar solicitudes de reseñas',

            // Reportes
            'view-reports' => 'Ver reportes',
            'export-reports' => 'Exportar reportes',

            // Configuración
            'view-settings' => 'Ver configuraciones',
            'edit-settings' => 'Editar configuraciones',

            // Impuestos
            'view-taxes' => 'Ver impuestos',
            'create-taxes' => 'Crear impuestos',
            'edit-taxes' => 'Editar impuestos',
            'publish-taxes' => 'Publicar impuestos',
            'soft-delete-taxes' => 'Eliminar impuestos (papelera)',
            'restore-taxes' => 'Restaurar impuestos eliminados',
            'hard-delete-taxes' => 'Eliminar impuestos permanentemente',

            // FAQs
            'view-faqs' => 'Ver FAQs',
            'create-faqs' => 'Crear FAQs',
            'edit-faqs' => 'Editar FAQs',
            'publish-faqs' => 'Publicar preguntas frecuentes',
            'soft-delete-faqs' => 'Eliminar FAQs (papelera)',
            'restore-faqs' => 'Restaurar FAQs eliminadas',
            'hard-delete-faqs' => 'Eliminar FAQs permanentemente',

            // Traducciones
            'view-translations' => 'Ver traducciones',
            'create-translations' => 'Crear traducciones',
            'edit-translations' => 'Editar traducciones',
            'delete-translations' => 'Eliminar traducciones',

            // Carritos
            'view-carts' => 'Ver carritos abandonados',
            'publish-carts' => 'Activar/Desactivar carritos',

            // Códigos promocionales
            'view-promo-codes' => 'Ver códigos promocionales',
            'create-promo-codes' => 'Crear códigos promocionales',
            'edit-promo-codes' => 'Editar códigos promocionales',
            'delete-promo-codes' => 'Eliminar códigos promocionales',

            // Políticas
            'view-policies' => 'Ver políticas',
            'edit-policies' => 'Editar políticas',

            // Policy Sections
            'view-policy-sections' => 'Ver secciones de políticas',
            'create-policy-sections' => 'Crear secciones de políticas',
            'edit-policy-sections' => 'Editar secciones de políticas',
            'publish-policy-sections' => 'Publicar secciones de políticas',
            'delete-policy-sections' => 'Eliminar secciones de políticas',

            // Categorías de clientes
            'view-customer-categories' => 'Ver categorías de clientes',
            'create-customer-categories' => 'Crear categorías de clientes',
            'edit-customer-categories' => 'Editar categorías de clientes',
            'publish-customer-categories' => 'Publicar categorías de clientes',
            'soft-delete-customer-categories' => 'Eliminar categorías (papelera)',
            'restore-customer-categories' => 'Restaurar categorías',
            'hard-delete-customer-categories' => 'Eliminar categorías permanentemente',

            // Pagos
            'view-payments' => 'Ver pagos',
            'edit-payments' => 'Gestionar pagos',

            // Tipos de tour
            'view-tour-types' => 'Ver tipos de tour',
            'create-tour-types' => 'Crear tipos de tour',
            'edit-tour-types' => 'Editar tipos de tour',
            'publish-tour-types' => 'Publicar/Ocultar tipos de tour',
            'soft-delete-tour-types' => 'Eliminar tipos de tour (papelera)',
            'restore-tour-types' => 'Restaurar tipos de tour',
            'hard-delete-tour-types' => 'Eliminar tipos de tour permanentemente',

            // Amenidades
            'view-amenities' => 'Ver amenidades',
            'create-amenities' => 'Crear amenidades',
            'edit-amenities' => 'Editar amenidades',
            'publish-amenities' => 'Publicar amenidades',
            'soft-delete-amenities' => 'Eliminar amenidades (papelera)',
            'restore-amenities' => 'Restaurar amenidades',
            'hard-delete-amenities' => 'Eliminar amenidades permanentemente',

            // Hoteles (Pickups)
            'view-hotels' => 'Ver lista de hoteles',
            'create-hotels' => 'Crear hoteles',
            'edit-hotels' => 'Editar hoteles',
            'publish-hotels' => 'Publicar hoteles',
            'delete-hotels' => 'Eliminar hoteles',

            // Puntos de Encuentro
            'view-meeting-points' => 'Ver puntos de encuentro',
            'create-meeting-points' => 'Crear puntos de encuentro',
            'edit-meeting-points' => 'Editar puntos de encuentro',
            'publish-meeting-points' => 'Publicar puntos de encuentro',
            'soft-delete-meeting-points' => 'Eliminar puntos de encuentro (papelera)',
            'restore-meeting-points' => 'Restaurar puntos de encuentro',
            'hard-delete-meeting-points' => 'Eliminar puntos de encuentro permanentemente',

            // Idiomas de Tours
            'view-tour-languages' => 'Ver idiomas de tours',
            'create-tour-languages' => 'Crear idiomas de tours',
            'edit-tour-languages' => 'Editar idiomas de tours',
            'publish-tour-languages' => 'Publicar idiomas de tours',
            'soft-delete-tour-languages' => 'Eliminar idiomas de tours (papelera)',
            'restore-tour-languages' => 'Restaurar idiomas de tours',
            'hard-delete-tour-languages' => 'Eliminar idiomas de tours permanentemente',

            // Itinerarios
            'view-itineraries' => 'Ver itinerarios',
            'create-itineraries' => 'Crear itinerarios',
            'edit-itineraries' => 'Editar itinerarios',
            'publish-itineraries' => 'Publicar itinerarios',
            'soft-delete-itineraries' => 'Eliminar itinerarios (papelera)',
            'restore-itineraries' => 'Restaurar itinerarios',
            'hard-delete-itineraries' => 'Eliminar itinerarios permanentemente',

            // Items de Itinerarios
            'view-itinerary-items' => 'Ver items de itinerario',
            'create-itinerary-items' => 'Crear items de itinerario',
            'edit-itinerary-items' => 'Editar items de itinerario',
            'publish-itinerary-items' => 'Publicar items de itinerario',
            'delete-itinerary-items' => 'Eliminar items de itinerario',

            // Horarios de Tours (Schedules)
            'view-tour-schedules' => 'Ver horarios de tours',
            'create-tour-schedules' => 'Crear horarios de tours',
            'edit-tour-schedules' => 'Editar horarios de tours',
            'publish-tour-schedules' => 'Publicar horarios de tours',
            'publish-assignments-tour-schedules' => 'Publicar asignaciones de horarios',
            'soft-delete-tour-schedules' => 'Eliminar horarios de tours (papelera)',
            'restore-tour-schedules' => 'Restaurar horarios de tours',
            'hard-delete-tour-schedules' => 'Eliminar horarios de tours permanentemente',

            // Disponibilidad de Tours (Extension)
            'create-tour-availability' => 'Crear disponibilidad de tours',
            'publish-tour-availability' => 'Publicar disponibilidad de tours',
            'delete-tour-availability' => 'Eliminar disponibilidad de tours',

            // Fechas Excluidas
            'view-tour-excluded-dates' => 'Ver fechas excluidas',
            'create-tour-excluded-dates' => 'Crear fechas excluidas',
            'edit-tour-excluded-dates' => 'Editar fechas excluidas',
            'publish-tour-excluded-dates' => 'Publicar fechas excluidas',
            'delete-tour-excluded-dates' => 'Eliminar fechas excluidas',

            // Precios de Tours (Granular)
            'view-tour-prices' => 'Ver precios de tours',
            'create-tour-prices' => 'Crear precios de tours',
            'edit-tour-prices' => 'Editar precios de tours',
            'publish-tour-prices' => 'Publicar precios de tours',
            'delete-tour-prices' => 'Eliminar precios de tours',

            // Email Templates
            'view-email-templates' => 'Ver plantillas de email',
            'edit-email-templates' => 'Editar plantillas de email',
        ];

        // Crear todos los permisos
        foreach ($permissions as $name => $description) {
            Permission::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web'],
                ['guard_name' => 'web'] // Solo para asegurar consistencia
            );
        }

        // Crear roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $customer = Role::firstOrCreate(['name' => 'customer', 'guard_name' => 'web']);

        // Super Admin tiene TODOS los permisos
        $superAdmin->syncPermissions(Permission::all());

        // Admin tiene TODOS los permisos (según requerimiento de usuario)
        // Usamos array_keys($permissions) para asegurar que tenga los mismos que definimos arriba
        // y evitar errores de typo manual.
        $adminPermissions = array_keys($permissions);

        // Opcional: Si hubiera que filtrar algo explícitamente:
        // $adminPermissions = array_filter($adminPermissions, function($p) { return $p !== 'dangerous-permission'; });
        
        // Sync permissions to ensure they're always up-to-date
        $admin->syncPermissions($adminPermissions);

        // Customer solo tiene permisos básicos (ningún acceso admin)
        // Los clientes no necesitan permisos especiales, solo acceso público
    }
}
