<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => '',
    'title_prefix' => 'GV |',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => true,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => 'Green Vacations CR',
    'logo_img' => 'images\logoCompanyWhite.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xl',
    'logo_img_alt' => 'Green Vacations CR',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => true,
        'img' => [
            'path' => 'images\logoCompanyWhite.png',
            'alt' => 'Auth Logo',
        'style' => 'padding: 10px;',
                'class' => 'mx-auto d-block',      // <-- centrado
        'width' => '50%',
        'height' => '50%',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

        'preloader' => [
        'enabled' => true,
        'mode' => 'fullscreen',
        'img' => [
            'path' => 'images\logoCompanyWhite.png',
            'alt' => 'Green Vacations Loading',
            'effect' => 'animation__shake',
            'width' => 60,
            'height' => 60,
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => true,
    'usermenu_header_class' => 'bg-success',
    'usermenu_image' => false,
    'usermenu_desc' => true,
    'usermenu_profile_url' => true,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => true,
    'layout_fixed_navbar' => true,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => true,
    'darkmode_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-primary',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-flat btn-primary',

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-dark navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => true,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => true,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 200,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

'use_route_url'       => false,
'dashboard_url'       => '/',
'logout_url'          => 'logout',
'login_url'           => 'login',
'register_url'        => 'register',
'password_reset_url'  => 'forgot-password',
'password_email_url'  => 'forgot-password',
'profile_url'         => false,
'disable_darkmode_routes' => false,
'home_url'            => 'home',

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

   'menu' => [

    // Widgets de la barra superior
    ['type' => 'navbar-search', 'text' => 'search', 'topnav_right' => false],
    ['type' => 'fullscreen-widget', 'topnav_right' => true],

    // Buscador del sidebar
    ['type' => 'sidebar-menu-search', 'text' => 'Buscar...'],

    // Acceso rápido
    [
        'text' => 'Inicio',
        'route' => 'admin.home',
        'icon' => 'fas fa-fw fa-home'
    ],
    [
        'text' => 'Mi Perfil',
        'route'=> 'profile.edit',
        'icon' => 'fas fa-fw fa-user',
    ],

    // ✅ CARRITOS
    [
        'text'    => 'Carritos',
        'icon'    => 'fas fa-shopping-cart',
        'submenu' => [
            [
                'text' => 'Mi Carrito',
                'route' => 'admin.carts.index',
                'icon'  => 'fas fa-shopping-cart',
            ],
            [
                'text' => 'Carritos Activos',
                'route' => 'admin.carts.all',
                'icon'  => 'fas fa-list',
            ],
        ],
    ],

    // ✅ CONFIGURACIÓN
    [
        'text'    => 'Configuración',
        'icon'    => 'fas fa-cogs',
        'submenu' => [
            [
                'text' => 'FAQ',
                'route' => 'admin.faqs.index',
                'icon' => 'fas fa-question-circle',
            ],
                        [
                'text' => 'Politicas',
                'route' => 'admin.policies.index',
                'icon' => 'fas fa-info-circle',
            ],
            [
                'text' => 'Generar Códigos Promocionales',
                'route' => 'admin.promoCodes.index',
                'icon' => 'fas fa-tags',
            ],
            [
                'text' => 'Traducciones',
                'route' => 'admin.translations.index',
                'icon' => 'fas fa-language',
            ],
            [
                'text' => 'Tipos de Tour',
                'route' => 'admin.tourtypes.index',
                'icon' => 'fas fa-tags',
            ],
            [
                'text'  => 'Cut-Off',
                'route' => 'admin.tours.cutoff.edit',
                'icon'  => 'fas fa-clock',
            ],

        ],
    ],

    // ✅ HOTELES
    [
        'text'    => 'Pickups',
        'icon'    => 'fas fa-hotel',
        'submenu' => [
            [
                'text' => 'Lista de Hoteles',
                'route' => 'admin.hotels.index',
                'icon'  => 'fas fa-list',
            ],
                        [
                'text'  => 'Meeting Points',
                'route' => 'admin.meetingpoints.index',
                'icon'  => 'fas fa-map-marker-alt',
            ],
        ],
    ],

    // ✅ RESERVAS
    [
        'text'    => 'Reservas',
        'icon'    => 'fas fa-calendar-check',
        'submenu' => [
            [
                'text' => 'Ver Reservas',
                'route' => 'admin.bookings.index',
                'icon'  => 'fas fa-calendar-check',
            ],
            // [
            //     'text' => 'Calendario',
            //     'route' => 'admin.bookings.calendar',
            //     'icon'  => 'fas fa-calendar-alt',
            // ],
            [
                'text'  => 'Bloquear Fechas',
                'route' => 'admin.tours.excluded_dates.index',
                'icon' => 'fas fa-ban',
            ],
        ],
    ],

    // ✅ TOURS
    [
        'text'    => 'Tours',
        'icon'    => 'fas fa-map-marked-alt',
        'submenu' => [
            [
                'text'  => 'Amenidades',
                'route' => 'admin.tours.amenities.index',
                'icon'  => 'fas fa-concierge-bell',
            ],
            [
                'text'  => 'Horarios',
                'route' => 'admin.tours.schedule.index',
                'icon'  => 'fas fa-calendar-alt',
            ],
            [
                'text'  => 'Idiomas',
                'route' => 'admin.languages.index',
                'icon'  => 'fas fa-globe',
            ],
            [
                'text'  => 'Itinerarios',
                'route' => 'admin.tours.itinerary.index',
                'icon'  => 'fas fa-route',
            ],
            [
                'text'  => 'Todos los Tours',
                'route' => 'admin.tours.index',
                'icon'  => 'fas fa-map-marked-alt',
            ],

            [
            'text'  => 'Ordenar Tours',
            'route' => 'admin.tours.order.index',
            'icon'  => 'fas fa-sort-amount-down',
            // opcional si usas gates/roles:
            // 'can'   => 'manage-tours',
        ],

        ],

    ],

    [
         'text'    => 'Imagenes',
        'icon'    => 'fas fa-camera',
        'submenu' => [
[
                'text'  => 'Imágenes de Tours',
                'route' => 'admin.tours.images.pick',
                'icon'  => 'fas fa-images',
            ],
            [
                'text'  => 'Covers de Categorías',
                'route' => 'admin.types.images.pick',
                'icon'  => 'fas fa-image',
            ],
    ],
],

    // ✅ USUARIOS
    [
        'text'    => 'Usuarios',
        'icon'    => 'fas fa-users-cog',
        'submenu' => [
            [
                'text' => 'Usuarios',
                'route'=> 'admin.users.index',
                'icon' => 'fas fa-user-cog',
                'can'  => 'is-admin',
            ],
            [
                'text' => 'Roles',
                'route'=> 'admin.roles.index',
                'icon' => 'fas fa-user-shield',
                'can'  => 'is-admin',
            ],
        ],
    ],
// ✅ RESEÑAS
[
    'text'    => 'Reseñas',
    'icon'    => 'fas fa-star',
    'can'     => 'manage-reviews',
    'submenu' => [
        [
            'text'  => 'Todos los Reviews',
            'route' => 'admin.reviews.index',
            'icon'  => 'fas fa-star',
        ],

        [
            'text'  => 'Proveedores',
            'route' => 'admin.review-providers.index',
            'icon'  => 'fas fa-plug',
        ],
        [
            'text'  => 'Solicitar Reviews',
            'route' => 'admin.review-requests.index',
            'icon'  => 'fas fa-envelope-open-text',
        ],
    ],
],

            [
                'text' => 'Reportes',
                'url'  => 'admin/reports',
                'icon' => 'fas fa-chart-line',
            ],


[
    'text'         => '🌐',
    'icon'         => false,
    'topnav_right' => true,
    'submenu'      => [
        [ 'text' => 'Español',   'route' => ['switch.language', ['language' => 'es']] ],
        [ 'text' => 'English',   'route' => ['switch.language', ['language' => 'en']] ],
        [ 'text' => 'Français',  'route' => ['switch.language', ['language' => 'fr']] ],
        [ 'text' => 'Português', 'route' => ['switch.language', ['language' => 'pt']] ],
        [ 'text' => 'Deutsch',   'route' => ['switch.language', ['language' => 'de']] ],
    ],
],

],


    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => true,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => true,
                    'location' => 'vendor/sweetalert2/sweetalert2.all.min.js',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];
