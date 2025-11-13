<?php
/*************************************************************
 *  MÓDULO DE CONFIGURAÇÃO – TRADUÇÕES (PT-BR)
 *  Arquivo: resources/lang/pt_BR/m_config.php
 *
 *  Índice (âncoras pesquisáveis)
 *  [01] POLICIES LINHA 20
 *  [02] TOURTYPES LINHA 139
 *  [03] FAQ LINHA 198
 *  [04] TRANSLATIONS LINHA 249
 *  [05] PROMOCODE LINHA 359
 *  [06] CUT-OFF LINHA 436
 *************************************************************/

return [

    // =========================================================
    // ==== POLICIES ===========================================
    // =========================================================
    'policies' => [
        // Títulos / cabeçalhos
        'categories_title'        => 'Políticas',
        'sections_title'          => 'Seções',

        // Colunas / campos comuns
        'id'                      => 'ID',
        'internal_name'           => 'Nome interno',
        'title_current_locale'    => 'Título',
        'validity_range'          => 'Período de vigência',
        'valid_from'              => 'Válida a partir de',
        'valid_to'                => 'Válida até',
        'status'                  => 'Status',
        'sections'                => 'Seções',
        'actions'                 => 'Ações',
        'active'                  => 'Ativa',
        'inactive'                => 'Inativa',
        'slug'                    => 'URL',
        'slug_hint'               => 'opcional',
        'slug_auto_hint'          => 'Será gerada automaticamente a partir do nome se deixada em branco.',
        'slug_edit_hint'          => 'Altera a URL da política. Use apenas letras minúsculas, números e hífens.',
        'updated'                 => 'Política atualizada com sucesso.',
        'propagate_to_all_langs' => 'Propagar esta alteração para todos os idiomas (EN, FR, DE, PT)',
        'propagate_hint'         => 'Será traduzida automaticamente a partir do texto atual e irá sobrescrever as traduções existentes nesses idiomas.',
        'update_base_es'         => 'Atualizar também a base (ES)',
        'update_base_hint'       => 'Sobrescreve o nome e o conteúdo da política na tabela base (espanhol). Use apenas se você também quiser alterar o texto original.',

        // Lista de categorias: ações
        'new_category'            => 'Nova categoria',
        'view_sections'           => 'Ver seções',
        'edit'                    => 'Editar',
        'activate_category'       => 'Ativar categoria',
        'deactivate_category'     => 'Desativar categoria',
        'delete'                  => 'Excluir',
        'delete_category_confirm' => 'Excluir esta categoria e TODAS as suas seções?<br>Esta ação não pode ser desfeita.',
        'no_categories'           => 'Nenhuma categoria encontrada.',
        'edit_category'           => 'Editar categoria',

        // Formulários (categoria)
        'title_label'             => 'Título',
        'description_label'       => 'Descrição',
        'register'                => 'Criar',
        'save_changes'            => 'Salvar alterações',
        'close'                   => 'Fechar',

        // Seções
        'back_to_categories'      => 'Voltar para categorias',
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
        'base_content_hint'       => 'Este é o texto principal da política. Ao criar, ele será traduzido automaticamente para outros idiomas, mas você poderá editar cada tradução depois.',

        // Público
        'page_title'              => 'Políticas',
        'no_policies'             => 'Não há políticas disponíveis no momento.',
        'section'                 => 'Seção',
        'cancellation_policy'     => 'Política de cancelamento',
        'refund_policy'           => 'Política de reembolso',
        'no_cancellation_policy'  => 'Nenhuma política de cancelamento configurada.',
        'no_refund_policy'        => 'Nenhuma política de reembolso configurada.',

        // Mensagens (categorias)
        'category_created'        => 'Categoria criada com sucesso.',
        'category_updated'        => 'Categoria atualizada com sucesso.',
        'category_activated'      => 'Categoria ativada com sucesso.',
        'category_deactivated'    => 'Categoria desativada com sucesso.',
        'category_deleted'        => 'Categoria excluída com sucesso.',

        // --- NOVAS CHAVES (refactor / utilidades) ---
        'untitled'                => 'Sem título',
        'no_content'              => 'Nenhum conteúdo disponível.',
        'display_name'            => 'Nome de exibição',
        'name'                    => 'Nome',
        'name_base'               => 'Nome base',
        'name_base_help'          => 'Identificador curto/slug da seção (apenas uso interno).',
        'translation_content'     => 'Conteúdo',
        'locale'                  => 'Idioma',
        'save'                    => 'Salvar',
        'name_base_label'         => 'Nome base',
        'translation_name'        => 'Nome traduzido',
        'lang_autodetect_hint'    => 'Você pode escrever em qualquer idioma; ele será detectado automaticamente.',
        'bulk_edit_sections'      => 'Edição rápida de seções',
        'bulk_edit_hint'          => 'As alterações em todas as seções serão salvas junto com a tradução da categoria quando você clicar em "Salvar".',
        'no_changes_made'         => 'Nenhuma alteração foi realizada.',
        'no_sections_found'       => 'Nenhuma seção encontrada.',
        'editing_locale'          => 'Editando',

        // Mensagens (seções)
        'section_created'         => 'Seção criada com sucesso.',
        'section_updated'         => 'Seção atualizada com sucesso.',
        'section_activated'       => 'Seção ativada com sucesso.',
        'section_deactivated'     => 'Seção desativada com sucesso.',
        'section_deleted'         => 'Seção excluída com sucesso.',

        // Mensagens genéricas do módulo
        'created_success'         => 'Criado com sucesso.',
        'updated_success'         => 'Atualizado com sucesso.',
        'deleted_success'         => 'Excluído com sucesso.',
        'activated_success'       => 'Ativado com sucesso.',
        'deactivated_success'     => 'Desativado com sucesso.',
        'unexpected_error'        => 'Ocorreu um erro inesperado.',

        // Botões / textos comuns (SweetAlert)
        'create'                  => 'Criar',
        'activate'                => 'Ativar',
        'deactivate'              => 'Desativar',
        'cancel'                  => 'Cancelar',
        'ok'                      => 'OK',
        'validation_errors'       => 'Há erros de validação',
        'error_title'             => 'Erro',

        // Confirmações específicas de seção
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
        // Títulos / cabeçalhos
        'title'                   => 'Tipos de tour',
        'new'                     => 'Adicionar tipo de tour',

        // Colunas / campos
        'id'                      => 'ID',
        'name'                    => 'Nome',
        'description'             => 'Descrição',
        'duration'                => 'Duração',
        'status'                  => 'Status',
        'actions'                 => 'Ações',
        'active'                  => 'Ativo',
        'inactive'                => 'Inativo',

        // Botões / ações
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
        'edit_title'              => 'Editar tipo de tour',
        'create_title'            => 'Criar tipo de tour',

        // Placeholders / dicas
        'examples_placeholder'    => 'Ex.: Aventura, Natureza, Relax',
        'duration_placeholder'    => 'Ex.: 4 horas, 8 horas',
        'suggested_duration_hint' => 'Formato sugerido: "4 horas", "8 horas".',
        'keep_default_hint'       => 'Deixe "4 horas" se for adequado; você pode alterar.',
        'optional'                => 'opcional',

        // Confirmações
        'confirm_delete'          => 'Tem certeza de que deseja excluir ":name"? Esta ação não pode ser desfeita.',
        'confirm_activate'        => 'Tem certeza de que deseja ativar ":name"?',
        'confirm_deactivate'      => 'Tem certeza de que deseja desativar ":name"?',

        // Mensagens (flash)
        'created_success'         => 'Tipo de tour criado com sucesso.',
        'updated_success'         => 'Tipo de tour atualizado com sucesso.',
        'deleted_success'         => 'Tipo de tour excluído com sucesso.',
        'activated_success'       => 'Tipo de tour ativado com sucesso.',
        'deactivated_success'     => 'Tipo de tour desativado com sucesso.',
        'in_use_error'            => 'Não foi possível excluir: este tipo de tour está em uso.',
        'unexpected_error'        => 'Ocorreu um erro inesperado. Tente novamente.',

        // Validação / genéricos
        'validation_errors'       => 'Verifique os campos destacados.',
        'error_title'             => 'Erro',
    ],

    // =========================================================
    // ==== FAQ ================================================
    // =========================================================
    'faq' => [
        // Título / cabeçalho
        'title'            => 'FAQ',

        // Campos / colunas
        'question'         => 'Pergunta',
        'answer'           => 'Resposta',
        'status'           => 'Status',
        'actions'          => 'Ações',
        'active'           => 'Ativa',
        'inactive'         => 'Inativa',

        // Botões / ações
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

        // Confirmações
        'confirm_create'   => 'Criar esta pergunta frequente?',
        'confirm_edit'     => 'Salvar as alterações desta pergunta frequente?',
        'confirm_delete'   => 'Tem certeza de que deseja excluir esta pergunta frequente?<br>Esta ação não pode ser desfeita.',
        'confirm_activate' => 'Tem certeza de que deseja ativar esta pergunta frequente?',
        'confirm_deactivate'=> 'Tem certeza de que deseja desativar esta pergunta frequente?',

        // Validação / erros
        'validation_errors'=> 'Há erros de validação',
        'error_title'      => 'Erro',

        // Mensagens (flash)
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
        // Títulos / textos gerais
        'title'                 => 'Gestão de traduções',
        'index_title'           => 'Gestão de traduções',
        'select_entity_title'   => 'Selecionar :entity para traduzir',
        'edit_title'            => 'Editar tradução',
        'main_information'      => 'Informações principais',
        'ok'                    => 'OK',
        'save'                  => 'Salvar',
        'validation_errors'     => 'Há erros de validação',
        'updated_success'       => 'Tradução atualizada com sucesso.',
        'unexpected_error'      => 'Não foi possível atualizar a tradução.',

        'editing'            => 'Editando',
        'policy_name'        => 'Nome da política',
        'policy_content'     => 'Conteúdo',
        'policy_sections'    => 'Seções da política',
        'section'            => 'Seção',
        'section_name'       => 'Nome da seção',
        'section_content'    => 'Conteúdo da seção',

        // Seletor de idioma (tela e helpers)
        'choose_locale_title'   => 'Selecionar idioma',
        'choose_locale_hint'    => 'Selecione o idioma para o qual você deseja traduzir este item.',
        'select_language_title' => 'Selecionar idioma',
        'select_language_intro' => 'Selecione o idioma para o qual você deseja traduzir este item.',
        'languages' => [
            'es' => 'Espanhol',
            'en' => 'Inglês',
            'fr' => 'Francês',
            'pt' => 'Português',
            'de' => 'Alemão',
        ],

        // Listagens / botões
        'select'                => 'Selecionar',
        'id_unavailable'        => 'ID não disponível',
        'no_items'              => 'Não há :entity disponíveis para traduzir.',

        // Campos comuns dos formulários de tradução
        'name'                  => 'Nome',
        'description'           => 'Descrição',
        'content'               => 'Conteúdo',
        'overview'              => 'Resumo',
        'itinerary'             => 'Roteiro',
        'itinerary_name'        => 'Nome do roteiro',
        'itinerary_description' => 'Descrição do roteiro',
        'itinerary_items'       => 'Itens do roteiro',
        'item'                  => 'Item',
        'item_title'            => 'Título do item',
        'item_description'      => 'Descrição do item',
        'sections'              => 'Seções',
        'edit'                  => 'Editar',
        'close'                 => 'Fechar',
        'actions'               => 'Ações',

        // === Rótulos MODULARES por campo ======================
        // Use como: __('m_config.translations.fields.<campo>')
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

            // Roteiro / itens (partial de tours)
            'itinerary'             => 'Roteiro',
            'itinerary_name'        => 'Nome do roteiro',
            'itinerary_description' => 'Descrição do roteiro',
            'item'                  => 'Item',
            'item_title'            => 'Título do item',
            'item_description'      => 'Descrição do item',
        ],

        // === Overrides por ENTIDADE e CAMPO (opcional) =======
        // No blade: primeiro tenta entity_fields.<type>.<field>,
        // se não existir, usa fields.<field>.
        'entity_fields' => [
            'tour_types' => [
                'duration' => 'Duração sugerida',
                'name'     => 'Nome do tipo de tour',
            ],
            'faqs' => [
                'question' => 'Pergunta (visível para o cliente)',
                'answer'   => 'Resposta (visível para o cliente)',
            ],
        ],

        // Nomes das entidades (plural)
        'entities' => [
            'tours'            => 'Tours',
            'itineraries'      => 'Roteiros',
            'itinerary_items'  => 'Itens do roteiro',
            'amenities'        => 'Comodidades',
            'faqs'             => 'Perguntas frequentes',
            'policies'         => 'Políticas',
            'tour_types'       => 'Tipos de tour',
        ],

        // Nomes das entidades (singular)
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
    'title'        => 'Códigos promocionais',
    'create_title' => 'Gerar novo código promocional',
    'list_title'   => 'Códigos promocionais existentes',

    'success_title' => 'Sucesso',
    'error_title'   => 'Erro',

    'fields' => [
        'code'        => 'Código',
        'discount'    => 'Desconto',

        'type'        => 'Tipo',
        'operation'   => 'Operação',
        'valid_from'  => 'Válido a partir de',
        'valid_until' => 'Válido até',
        'usage_limit' => 'Limite de uso',
        'promocode_hint'        => 'Após aplicar, o cupom será salvo ao enviar o formulário e os snapshots do histórico serão atualizados.',
    ],

    'types' => [
        'percent' => '%',
        'amount'  => '$',
    ],

    'symbols' => [
        'percent'  => '%',
        'currency' => '$',
    ],

    'table' => [
        'code'         => 'Código',
        'discount'     => 'Desconto',
        'operation'    => 'Operação',
        'validity'     => 'Vigência',
        'date_status'  => 'Status (data)',
        'usage'        => 'Usos',
        'usage_status' => 'Status (uso)',
        'actions'      => 'Ações',
    ],

    'status' => [
        'used'      => 'Utilizado',
        'available' => 'Disponível',
    ],

    'date_status' => [
        'scheduled' => 'Agendado',
        'active'    => 'Ativo',
        'expired'   => 'Expirado',
    ],

    'actions' => [
        'generate' => 'Gerar',
        'delete'   => 'Excluir',
        'toggle_operation' => 'Alternar entre Somar/Subtrair',
    ],

    'labels' => [
        'unlimited_placeholder' => 'Vazio = ilimitado',
        'unlimited_hint'        => 'Deixe em branco para usos ilimitados. Defina 1 para uso único.',
        'no_limit'              => '(sem limite)',
        'remaining'             => 'restantes',
    ],

    'confirm_delete' => 'Tem certeza de que deseja excluir este código?',
    'empty'          => 'Não há códigos promocionais disponíveis.',

    'messages' => [
        'created_success'         => 'Código promocional criado com sucesso.',
        'deleted_success'         => 'Código promocional excluído com sucesso.',
        'percent_over_100'        => 'A porcentagem não pode ser maior que 100.',
        'code_exists_normalized'  => 'Este código (ignorando espaços e maiúsculas/minúsculas) já existe.',
        'invalid_or_used'         => 'Código inválido ou já utilizado.',
        'valid'                   => 'Código válido.',
        'server_error'            => 'Erro no servidor, tente novamente.',
        'operation_updated'       => 'Operação atualizada com sucesso.',
    ],

    'operations' => [
        'add'            => 'Somar',
        'subtract'       => 'Subtrair',
        'make_add'       => 'Mudar para "Somar"',
        'make_subtract'  => 'Mudar para "Subtrair"',
        'surcharge'      => 'Acréscimo',
        'discount'       => 'Desconto',
    ],
],

// =========================================================
// ==== CUTOFF =============================================
// =========================================================
'cut-off' => [
    // Títulos / cabeçalhos
    'title'       => 'Cut-off',
    'header'      => 'Configuração de Cut-off',
    'server_time' => 'Hora do servidor (:tz)',

    // Abas
    'tabs' => [
        'global'   => 'Global (padrão)',
        'tour'     => 'Bloqueio por tour',
        'schedule' => 'Bloqueio por horário',
        'summary'  => 'Resumo',
        'help'     => 'Ajuda',
    ],

    // Campos
    'fields' => [
        'cutoff_hour'       => 'Hora de corte (24h)',
        'cutoff_hour_short' => 'Cut-off (24h)',
        'lead_days'         => 'Dias de antecedência',
        'timezone'          => 'Fuso horário',
        'tour'              => 'Tour',
        'schedule'          => 'Horário',
        'actions'           => 'Ações'
    ],

    // Selects / placeholders
    'selects' => [
        'tour' => '— Selecione um tour —',
        'time' => '— Selecione um horário —',
    ],

    // Rótulos
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

    // Ações
    'actions' => [
        'save_global'   => 'Salvar global',
        'save_tour'     => 'Salvar bloqueio do tour',
        'save_schedule' => 'Salvar bloqueio do horário',
        'clear'         => 'Limpar bloqueio',
        'confirm'       => 'Confirmar',
        'cancel'        => 'Cancelar',
    ],

    // Confirmações (modais)
    'confirm' => [
        'tour' => [
            'title' => 'Salvar bloqueio do tour?',
            'text'  => 'Será aplicado um bloqueio específico para este tour. Deixe em branco para herdar.',
        ],
        'schedule' => [
            'title' => 'Salvar bloqueio do horário?',
            'text'  => 'Será aplicado um bloqueio específico para este horário. Deixe em branco para herdar.',
        ],
    ],

    // Resumo
    'summary' => [
        'tour_title'            => 'Bloqueios por tour',
        'no_tour_overrides'     => 'Não há bloqueios em nível de tour.',
        'schedule_title'        => 'Bloqueios por horário',
        'no_schedule_overrides' => 'Não há bloqueios em nível de horário.',
        'search_placeholder'    => 'Buscar tour ou horário…',
    ],

    // Flash / toasts
    'flash' => [
        'success_title' => 'Sucesso',
        'error_title'   => 'Erro',
    ],

    // Ajuda
    'help' => [
        'title'      => 'Como funciona?',
        'global'     => 'Valor padrão para todo o site.',
        'tour'       => 'Se um tour tem cutoff/dias configurados, ele tem prioridade sobre o Global.',
        'schedule'   => 'Se um horário do tour tem bloqueio, ele tem prioridade sobre o Tour.',
        'precedence' => 'Precedência',
    ],

    // Dicas / hints
    'hints' => [
        // Usados em Global
        'cutoff_example'    => 'Ex.: :ex. Depois desse horário, "hoje" deixa de estar disponível.',
        'pattern_24h'       => 'Formato 24h HH:MM (ex.: 09:30, 18:00).',
        'cutoff_behavior'   => 'Se a hora de corte já tiver passado, a data mais próxima disponível passa a ser o dia seguinte.',
        'lead_days'         => 'Número mínimo de dias de antecedência (0 permite reservar para hoje se a hora de corte ainda não passou).',
        'lead_days_detail'  => 'Faixa permitida: 0–30. 0 permite reserva no mesmo dia se a hora de corte não tiver sido atingida.',
        'timezone_source'   => 'É obtido de config(\'app.timezone\').',

        // Usados em Tour
        'pick_tour'             => 'Selecione primeiro um tour; depois defina o bloqueio (opcional).',
        'tour_override_explain' => 'Se você definir apenas um valor (cutoff ou dias), o outro herda do Global.',
        'clear_button_hint'     => 'Use "Limpar bloqueio" para voltar a herdar.',
        'leave_empty_inherit'   => 'Deixe em branco para herdar.',

        // Usados em Schedule
        'pick_schedule'             => 'Depois selecione o horário do tour.',
        'schedule_override_explain' => 'Os valores definidos aqui têm prioridade sobre os do Tour. Deixe em branco para herdar.',
        'schedule_precedence_hint'  => 'Precedência: Horário → Tour → Global.',

        // Usados em Resumo
        'dash_means_inherit' => 'O símbolo "—" indica que o valor é herdado.',
    ],
],

];
