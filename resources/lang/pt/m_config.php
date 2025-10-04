<?php
/*************************************************************
 *  Índice (anclas buscables)
 *  [01] POLICIES LÍNEA 20
 *  [02] TOURTYPES LÍNEA 139
 *  [03] FAQ LÍNEA 198
 *  [04] TRANSLATIONS LÍNEA 249
 *  [05] PROMOCODE LÍNEA 359
 *  [06] CUT-OFF LÍNEA 436
 *************************************************************/

return [

    // =========================================================
    // ==== POLICIES ===========================================
    // =========================================================
    'policies' => [
        // Títulos / encabezados
        'categories_title'        => 'Categorias de Políticas',
        'sections_title'          => 'Seções — :policy',

        // Columnas / campos comunes
        'id'                      => 'ID',
        'internal_name'           => 'Nome interno',
        'title_current_locale'    => 'Título',
        'validity_range'          => 'Período de vigência',
        'valid_from'              => 'Válida a partir de',
        'valid_to'                => 'Válida até',
        'status'                  => 'Status',
        'sections'                => 'Seções',
        'actions'                 => 'Ações',
        'active'                  => 'Ativo',
        'inactive'                => 'Inativo',
        'slug'                    => 'URL',
        'slug_hint'               => 'opcional',
        'slug_auto_hint'          => 'Será gerado automaticamente a partir do nome se deixado vazio',
        'slug_edit_hint'          => 'Altere a URL da política. Use apenas letras minúsculas, números e hífens.',
        'updated'                  => 'Política atualizada com sucesso.',



        // Lista de categorías: acciones
        'new_category'            => 'Nova categoria',
        'view_sections'           => 'Ver seções',
        'edit'                    => 'Editar',
        'activate_category'       => 'Ativar categoria',
        'deactivate_category'     => 'Desativar categoria',
        'delete'                  => 'Excluir',
        'delete_category_confirm' => 'Excluir esta categoria e TODAS as suas seções?<br>Esta ação não pode ser desfeita.',
        'no_categories'           => 'Nenhuma categoria encontrada.',
        'edit_category'           => 'Editar categoria',

        // Formularios (categoría)
        'title_label'             => 'Título',
        'description_label'       => 'Descrição',
        'register'                => 'Criar',
        'save_changes'            => 'Salvar alterações',
        'close'                   => 'Fechar',

        // Secciones
        'back_to_categories'      => 'Voltar às categorias',
        'new_section'             => 'Nova seção',
        'key'                     => 'Chave',
        'order'                   => 'Ordem',
        'activate_section'        => 'Ativar seção',
        'deactivate_section'      => 'Desativar seção',
        'delete_section_confirm'  => 'Tem certeza de que deseja excluir esta seção?<br>Esta ação não pode ser desfeita.',
        'no_sections'             => 'Nenhuma seção encontrada.',
        'edit_section'            => 'Editar seção',
        'internal_key_optional'   => 'Chave interna (opcional)',
        'content_label'           => 'Conteúdo',
        'section_content'         => 'Conteúdo',
        'base_content_hint'       => 'Este é o texto principal da política. Ele será traduzido automaticamente para outros idiomas quando criado, mas você pode editar cada tradução posteriormente.',


        // Público
        'page_title'              => 'Políticas',
        'no_policies'             => 'Não há políticas disponíveis no momento.',
        'section'                 => 'Seção',
        'cancellation_policy'     => 'Política de cancelamento',
        'refund_policy'           => 'Política de reembolso',
        'no_cancellation_policy'  => 'Nenhuma política de cancelamento configurada.',
        'no_refund_policy'        => 'Nenhuma política de reembolso configurada.',

        // Mensajes (categorías)
        'category_created'        => 'Categoria criada com sucesso.',
        'category_updated'        => 'Categoria atualizada com sucesso.',
        'category_activated'      => 'Categoria ativada com sucesso.',
        'category_deactivated'    => 'Categoria desativada com sucesso.',
        'category_deleted'        => 'Categoria excluída com sucesso.',

        // --- NUEVAS CLAVES (refactor / utilidades) ---
        'untitled'                => 'Sem título',
        'no_content'              => 'Sem conteúdo disponível.',
        'display_name'            => 'Nome de exibição',
        'name'                    => 'Nome',
        'name_base'               => 'Nome base',
        'name_base_help'          => 'Identificador curto/slug da seção (apenas interno).',
        'translation_content'     => 'Conteúdo',
        'locale'                  => 'Idioma',
        'save'                    => 'Salvar',
        'name_base_label'         => 'Nome base',
        'translation_name'        => 'Nome traduzido',
        'lang_autodetect_hint'    => 'Você pode escrever em qualquer idioma; é detectado automaticamente.',
        'bulk_edit_sections'      => 'Edição rápida de seções',
        'bulk_edit_hint'          => 'As alterações em todas as seções serão salvas junto com a tradução da categoria quando você clicar em "Salvar".',
        'no_changes_made'         => 'Nenhuma alteração realizada.',
        'no_sections_found'       => 'Nenhuma seção encontrada.',
        'editing_locale'          => 'Editando',

        // Mensajes (secciones)
        'section_created'         => 'Seção criada com sucesso.',
        'section_updated'         => 'Seção atualizada com sucesso.',
        'section_activated'       => 'Seção ativada com sucesso.',
        'section_deactivated'     => 'Seção desativada com sucesso.',
        'section_deleted'         => 'Seção excluída com sucesso.',

        // Mensajes genéricos del módulo
        'created_success'         => 'Criado com sucesso.',
        'updated_success'         => 'Atualizado com sucesso.',
        'deleted_success'         => 'Excluído com sucesso.',
        'activated_success'       => 'Ativado com sucesso.',
        'deactivated_success'     => 'Desativado com sucesso.',
        'unexpected_error'        => 'Ocorreu um erro inesperado.',

        // Botones / textos comunes (SweetAlert)
        'create'                  => 'Criar',
        'activate'                => 'Ativar',
        'deactivate'              => 'Desativar',
        'cancel'                  => 'Cancelar',
        'ok'                      => 'OK',
        'validation_errors'       => 'Há erros de validação',
        'error_title'             => 'Erro',

        // Confirmaciones específicas de sección
        'confirm_create_section'      => 'Criar esta seção?',
        'confirm_edit_section'        => 'Salvar as alterações desta seção?',
        'confirm_deactivate_section'  => 'Tem certeza de que deseja desativar esta seção?',
        'confirm_activate_section'    => 'Tem certeza de que deseja ativar esta seção?',
        'confirm_delete_section'      => 'Tem certeza de que deseja excluir esta seção?<br>Esta ação não pode ser desfeita.',
    ],

    // =========================================================
    // ==== TOURTYPES ==========================================
    // =========================================================
    'tourtypes' => [
        // Títulos / encabezados
        'title'                   => 'Tipos de Tour',
        'new'                     => 'Adicionar Tipo de Tour',

        // Columnas / campos
        'id'                      => 'ID',
        'name'                    => 'Nome',
        'description'             => 'Descrição',
        'duration'                => 'Duração',
        'status'                  => 'Status',
        'actions'                 => 'Ações',
        'active'                  => 'Ativo',
        'inactive'                => 'Inativo',

        // Botones / acciones
        'register'                => 'Salvar',
        'update'                  => 'Atualizar',
        'save'                    => 'Salvar',
        'close'                   => 'Fechar',
        'cancel'                  => 'Cancelar',
        'edit'                    => 'Editar',
        'delete'                  => 'Excluir',
        'activate'                => 'Ativar',
        'deactivate'              => 'Desativar',

        // Títulos de modal
        'edit_title'              => 'Editar Tipo de Tour',
        'create_title'            => 'Criar Tipo de Tour',

        // Placeholders / ayudas
        'examples_placeholder'    => 'Ex.: Aventura, Natureza, Relaxamento',
        'duration_placeholder'    => 'Ex.: 4 horas, 8 horas',
        'suggested_duration_hint' => 'Formato sugerido: "4 horas", "8 horas".',
        'keep_default_hint'       => 'Deixe "4 horas" se aplicar; você pode alterar.',
        'optional'                => 'opcional',

        // Confirmaciones
        'confirm_delete'          => 'Tem certeza de que deseja excluir ":name"? Esta ação não pode ser desfeita.',
        'confirm_activate'        => 'Tem certeza de que deseja ativar ":name"?',
        'confirm_deactivate'      => 'Tem certeza de que deseja desativar ":name"?',

        // Mensajes (flash)
        'created_success'         => 'Tipo de tour criado com sucesso.',
        'updated_success'         => 'Tipo de tour atualizado com sucesso.',
        'deleted_success'         => 'Tipo de tour excluído com sucesso.',
        'activated_success'       => 'Tipo de tour ativado com sucesso.',
        'deactivated_success'     => 'Tipo de tour desativado com sucesso.',
        'in_use_error'            => 'Não foi possível excluir: este tipo de tour está em uso.',
        'unexpected_error'        => 'Ocorreu um erro inesperado. Tente novamente.',

        // Validación / genéricos
        'validation_errors'       => 'Revise os campos destacados.',
        'error_title'             => 'Erro',
    ],

    // =========================================================
    // ==== FAQ ================================================
    // =========================================================
    'faq' => [
        // Título / cabecera
        'title'            => 'Perguntas Frequentes',

        // Campos / columnas
        'question'         => 'Pergunta',
        'answer'           => 'Resposta',
        'status'           => 'Status',
        'actions'          => 'Ações',
        'active'           => 'Ativo',
        'inactive'         => 'Inativo',

        // Botones / acciones
        'new'              => 'Nova pergunta',
        'create'           => 'Criar',
        'save'             => 'Salvar',
        'edit'             => 'Editar',
        'delete'           => 'Excluir',
        'activate'         => 'Ativar',
        'deactivate'       => 'Desativar',
        'cancel'           => 'Cancelar',
        'close'            => 'Fechar',
        'ok'               => 'OK',

        // UI
        'read_more'        => 'Ler mais',
        'read_less'        => 'Ler menos',

        // Confirmaciones
        'confirm_create'   => 'Criar esta pergunta frequente?',
        'confirm_edit'     => 'Salvar as alterações desta pergunta frequente?',
        'confirm_delete'   => 'Tem certeza de que deseja excluir esta pergunta frequente?<br>Esta ação não pode ser desfeita.',
        'confirm_activate' => 'Tem certeza de que deseja ativar esta pergunta frequente?',
        'confirm_deactivate'=> 'Tem certeza de que deseja desativar esta pergunta frequente?',

        // Validación / errores
        'validation_errors'=> 'Há erros de validação',
        'error_title'      => 'Erro',

        // Mensajes (flash)
        'created_success'      => 'Pergunta frequente criada com sucesso.',
        'updated_success'      => 'Pergunta frequente atualizada com sucesso.',
        'deleted_success'      => 'Pergunta frequente excluída com sucesso.',
        'activated_success'    => 'Pergunta frequente ativada com sucesso.',
        'deactivated_success'  => 'Pergunta frequente desativada com sucesso.',
        'unexpected_error'     => 'Ocorreu um erro inesperado.',
    ],

    // =========================================================
    // ==== TRANSLATIONS =======================================
    // =========================================================
    'translations' => [
        // Títulos / textos generales
        'title'                 => 'Gerenciamento de Traduções',
        'index_title'           => 'Gerenciamento de traduções',
        'select_entity_title'   => 'Selecione :entity para traduzir',
        'edit_title'            => 'Editar tradução',
        'main_information'      => 'Informações principais',
        'ok'                    => 'OK',
        'save'                  => 'Salvar',
        'validation_errors'     => 'Há erros de validação',
        'updated_success'       => 'Tradução atualizada com sucesso.',
        'unexpected_error'      => 'Não foi possível atualizar a tradução.',
                'editing'         => 'Editando',
        'policy_name'     => 'Título da política',
        'policy_content'  => 'Conteúdo',
        'policy_sections' => 'Seções da política',
        'section'         => 'Seção',
        'section_name'    => 'Nome da seção',
        'section_content' => 'Conteúdo da seção',

        // Selector de idioma (pantalla y helpers)
        'choose_locale_title'   => 'Selecionar idioma',
        'choose_locale_hint'    => 'Selecione o idioma para o qual deseja traduzir este item.',
        'select_language_title' => 'Selecionar idioma',
        'select_language_intro' => 'Selecione o idioma para o qual deseja traduzir este item.',
        'languages' => [
            'es' => 'Espanhol',
            'en' => 'Inglês',
            'fr' => 'Francês',
            'pt' => 'Português',
            'de' => 'Alemão',
        ],

        // Listados / botones
        'select'                => 'Selecionar',
        'id_unavailable'        => 'ID indisponível',
        'no_items'              => 'Não há :entity disponíveis para traduzir.',

        // Campos comunes de formularios de traducción
        'name'                  => 'Nome',
        'description'           => 'Descrição',
        'content'               => 'Conteúdo',
        'overview'              => 'Resumo',
        'itinerary'             => 'Roteiro',
        'itinerary_name'        => 'Nome do Roteiro',
        'itinerary_description' => 'Descrição do Roteiro',
        'itinerary_items'       => 'Itens do Roteiro',
        'item'                  => 'Item',
        'item_title'            => 'Título do Item',
        'item_description'      => 'Descrição do Item',
        'sections'              => 'Seções',
        'edit'                  => 'Editar',
        'close'                 => 'Fechar',
        'actions'               => 'Ações',

        // === Etiquetas MODULARES por campo ====================
        'fields' => [
            // Genéricos
            'name'                  => 'Nome',
            'title'                 => 'Título',
            'overview'              => 'Resumo',
            'description'           => 'Descrição',
            'content'               => 'Conteúdo',
            'duration'              => 'Duração',
            'question'              => 'Pergunta',
            'answer'                => 'Resposta',

            // Itinerario / ítems (parcial de tours)
            'itinerary'             => 'Roteiro',
            'itinerary_name'        => 'Nome do roteiro',
            'itinerary_description' => 'Descrição do roteiro',
            'item'                  => 'Item',
            'item_title'            => 'Título do item',
            'item_description'      => 'Descrição do item',
        ],

        // === Overrides por ENTIDAD y CAMPO (opcional) =========
        'entity_fields' => [
            'tour_types' => [
                'duration' => 'Duração sugerida',
                'name'     => 'Nome do tipo de tour',
            ],
            'faqs' => [
                'question' => 'Pergunta (visível ao cliente)',
                'answer'   => 'Resposta (visível ao cliente)',
            ],
        ],

        // Nombres de entidades (plural)
        'entities' => [
            'tours'            => 'Tours',
            'itineraries'      => 'Roteiros',
            'itinerary_items'  => 'Itens do roteiro',
            'amenities'        => 'Comodidades',
            'faqs'             => 'Perguntas frequentes',
            'policies'         => 'Políticas',
            'tour_types'       => 'Tipos de tour',
        ],

        // Nombres de entidades (singular)
        'entities_singular' => [
            'tours'            => 'tour',
            'itineraries'      => 'roteiro',
            'itinerary_items'  => 'item do roteiro',
            'amenities'        => 'comodidade',
            'faqs'             => 'pergunta frequente',
            'policies'         => 'política',
            'tour_types'       => 'tipo de tour',
        ],
    ],

    // =========================================================
    // ==== PROMOCODE ==========================================
    // =========================================================
    'promocode' => [
        'title'        => 'Códigos Promocionais',
        'create_title' => 'Gerar novo código promocional',
        'list_title'   => 'Códigos promocionais existentes',

        'success_title' => 'Sucesso',
        'error_title'   => 'Erro',

        'fields' => [
            'code'        => 'Código',
            'discount'    => 'Desconto',
            'type'        => 'Tipo',
            'operation'   => 'Operação',
            'valid_from'  => 'Válido de',
            'valid_until' => 'Válido até',
            'usage_limit' => 'Limite de usos',
        ],

        'types' => [
            'percent' => '%',
            'amount'  => 'R$',
        ],

        'symbols' => [
            'percent'  => '%',
            'currency' => 'R$',
        ],

        'table' => [
            'code'         => 'Código',
            'discount'     => 'Desconto',
            'operation'    => 'Operação',
            'validity'     => 'Vigência',
            'date_status'  => 'Estado (data)',
            'usage'        => 'Usos',
            'usage_status' => 'Estado (uso)',
            'actions'      => 'Ações',
        ],

        'status' => [
            'used'      => 'Usado',
            'available' => 'Disponível',
        ],

        'date_status' => [
            'scheduled' => 'Programado',
            'active'    => 'Ativo',
            'expired'   => 'Expirado',
        ],

        'actions' => [
            'generate'         => 'Gerar',
            'delete'           => 'Excluir',
            'toggle_operation' => 'Alternar Somar/Subtrair',
        ],

        'labels' => [
            'unlimited_placeholder' => 'Vazio = ilimitado',
            'unlimited_hint'        => 'Deixe em branco para usos ilimitados. Coloque 1 para uso único.',
            'no_limit'              => '(sem limite)',
            'remaining'             => 'restantes',
        ],

        'confirm_delete' => 'Tem certeza de que deseja excluir este código?',
        'empty'          => 'Não há códigos promocionais disponíveis.',

        'messages' => [
            'created_success'        => 'Código promocional criado com sucesso.',
            'deleted_success'        => 'Código promocional excluído com sucesso.',
            'percent_over_100'       => 'A porcentagem não pode ser maior que 100.',
            'code_exists_normalized' => 'Este código (ignorando espaços e maiúsculas/minúsculas) já existe.',
            'invalid_or_used'        => 'Código inválido ou já utilizado.',
            'valid'                  => 'Código válido.',
            'server_error'           => 'Erro no servidor. Tente novamente.',
            'operation_updated'      => 'Operação atualizada com sucesso.',
        ],

        'operations' => [
            'add'           => 'Somar',
            'subtract'      => 'Subtrair',
            'make_add'      => 'Mudar para “Somar”',
            'make_subtract' => 'Mudar para “Subtrair”',
        ],
    ],


    // =========================================================
    // ==== CUTOFF =============================================
    // =========================================================
    'cut-off' => [
        // Títulos / encabezados
        'title'       => 'Configuração de Cut-off',
        'header'      => 'Configurações de Reserva',
        'server_time' => 'Hora do servidor (:tz)',

        // Tabs
        'tabs' => [
            'global'   => 'Global (padrão)',
            'tour'     => 'Bloqueio por Tour',
            'schedule' => 'Bloqueio por Horário',
            'summary'  => 'Resumo',
            'help'     => 'Ajuda',
        ],

        // Campos
        'fields' => [
            'cutoff_hour'       => 'Hora de corte (24h)',
            'cutoff_hour_short' => 'Cutoff (24h)',
            'lead_days'         => 'Dias de antecedência',
            'timezone'          => 'Fuso horário',
            'tour'              => 'Tour',
            'schedule'          => 'Horário',
        ],

        // Selects / placeholders
        'selects' => [
            'tour' => '— Selecione um tour —',
            'time' => '— Selecione um horário —',
        ],

        // Etiquetas
        'labels' => [
            'status' => 'Status',
        ],

        // Badges / chips
        'badges' => [
            'inherits'            => 'Herda Global',
            'override'            => 'Bloqueio',
            'inherit_tour_global' => 'Herda do Tour/Global',
            'schedule'            => 'Horário',
            'tour'                => 'Tour',
            'global'              => 'Global',
        ],

        // Acciones
        'actions' => [
            'save_global'   => 'Salvar global',
            'save_tour'     => 'Salvar bloqueio do tour',
            'save_schedule' => 'Salvar bloqueio do horário',
            'clear'         => 'Limpar bloqueio',
            'confirm'       => 'Confirmar',
            'cancel'        => 'Cancelar',
            'actions'      => 'Ações',
        ],

        // Confirmaciones (modales)
        'confirm' => [
            'tour' => [
                'title' => 'Salvar bloqueio do tour?',
                'text'  => 'Será aplicado um bloqueio específico para este tour. Deixe vazio para herdar.',
            ],
            'schedule' => [
                'title' => 'Salvar bloqueio do horário?',
                'text'  => 'Será aplicado um bloqueio específico para este horário. Deixe vazio para herdar.',
            ],
        ],

        // Resumen
        'summary' => [
            'tour_title'            => 'Bloqueios por Tour',
            'no_tour_overrides'     => 'Não há bloqueios no nível de tour.',
            'schedule_title'        => 'Bloqueios por Horário',
            'no_schedule_overrides' => 'Não há bloqueios no nível de horário.',
            'search_placeholder'    => 'Pesquisar tour ou horário…',
        ],

        // Flash / toasts
        'flash' => [
            'success_title' => 'Sucesso',
            'error_title'   => 'Erro',
        ],

        // Ayuda
        'help' => [
            'title'      => 'Como funciona?',
            'global'     => 'Valor padrão para todo o site.',
            'tour'       => 'Se um tour tem cutoff/dias configurados, tem prioridade sobre o Global.',
            'schedule'   => 'Se um horário do tour tem bloqueio, tem prioridade sobre o Tour.',
            'precedence' => 'Precedência',
        ],

        // Pistas / ayudas (hints)
        'hints' => [
            // usados en Global
            'cutoff_example'    => 'Ex.: :ex. Depois desse horário, “hoje” deixa de estar disponível.',
            'pattern_24h'       => 'Formato 24h HH:MM (ex. 09:30, 18:00).',
            'cutoff_behavior'   => 'Se a hora de corte já passou, a data disponível mais próxima passa para o dia seguinte.',
            'lead_days'         => 'Dias mínimos de antecedência (0 permite reservar hoje se ainda não passou do cutoff).',
            'lead_days_detail'  => 'Intervalo permitido: 0–30. 0 permite reservar no mesmo dia se ainda não passou a hora de corte.',
            'timezone_source'   => 'Vem de config(\'app.timezone\').',

            // usados en Tour
            'pick_tour'             => 'Escolha primeiro um tour; depois defina o bloqueio (opcional).',
            'tour_override_explain' => 'Se definir apenas um (cutoff ou dias), o outro herda do Global.',
            'clear_button_hint'     => 'Use “Limpar bloqueio” para voltar a herdar.',
            'leave_empty_inherit'   => 'Deixe vazio para herdar.',

            // usados en Schedule
            'pick_schedule'             => 'Depois selecione o horário do tour.',
            'schedule_override_explain' => 'Os valores aqui têm prioridade sobre os do Tour. Deixe vazio para herdar.',
            'schedule_precedence_hint'  => 'Precedência: Horário → Tour → Global.',

            // usados en Resumen
            'dash_means_inherit' => 'O símbolo “—” indica que o valor é herdado.',
        ],
    ],

];
