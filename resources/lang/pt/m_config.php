<?php
/*************************************************************
 *  MÓDULO DE CONFIGURAÇÃO – TRADUÇÕES (PT)
 *  Arquivo: resources/lang/pt/m_config.php
 *
 *  Índice (âncoras pesquisáveis)
 *  [01] POLICIES 16
 *  [02] TOURTYPES 134
 *  [03] FAQ 193
 *  [04] TRANSLATIONS 244
 *  [05] PROMOCODE 351
 *************************************************************/

return [

    // =========================================================
    // ==== POLICIES ===========================================
    // =========================================================
    'policies' => [
        // Títulos / cabeçalhos
        'categories_title'        => 'Categorias de Políticas',
        'sections_title'          => 'Seções — :policy',

        // Colunas / campos comuns
        'id'                      => 'ID',
        'internal_name'           => 'Nome interno',
        'title_current_locale'    => 'Título',
        'validity_range'          => 'Período de validade',
        'valid_from'              => 'Válida a partir de',
        'valid_to'                => 'Válida até',
        'status'                  => 'Status',
        'sections'                => 'Seções',
        'actions'                 => 'Ações',
        'active'                  => 'Ativo',
        'inactive'                => 'Inativo',

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

        // --- NOVAS CHAVES (refactor / utilitários) ---
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
        'lang_autodetect_hint'    => 'Você pode escrever em qualquer idioma; ele será detectado automaticamente.',
        'bulk_edit_sections'      => 'Edição rápida de seções',
        'bulk_edit_hint'          => 'As alterações em todas as seções serão salvas junto com a tradução da categoria quando você clicar em "Salvar".',
        'no_changes_made'         => 'Nenhuma alteração realizada.',
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

        // Confirmações específicas da seção
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
        'title'                   => 'Tipos de Tours',
        'new'                     => 'Adicionar Tipo de Tour',

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
        'edit_title'              => 'Editar Tipo de Tour',
        'create_title'            => 'Criar Tipo de Tour',

        // Placeholders / dicas
        'examples_placeholder'    => 'Ex.: Aventura, Natureza, Relax',
        'duration_placeholder'    => 'Ex.: 4 horas, 8 horas',
        'suggested_duration_hint' => 'Formato sugerido: "4 horas", "8 horas".',
        'keep_default_hint'       => 'Deixe "4 horas" se aplicável; você pode alterar.',
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
        'in_use_error'            => 'Não é possível excluir: este tipo de tour está em uso.',
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
        'title'            => 'Perguntas frequentes',

        // Campos / colunas
        'question'         => 'Pergunta',
        'answer'           => 'Resposta',
        'status'           => 'Status',
        'actions'          => 'Ações',
        'active'           => 'Ativo',
        'inactive'         => 'Inativo',

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
        'confirm_create'   => 'Criar esta pergunta?',
        'confirm_edit'     => 'Salvar as alterações desta pergunta?',
        'confirm_delete'   => 'Tem certeza de que deseja excluir esta pergunta?<br>Esta ação não pode ser desfeita.',
        'confirm_activate' => 'Tem certeza de que deseja ativar esta pergunta?',
        'confirm_deactivate'=> 'Tem certeza de que deseja desativar esta pergunta?',

        // Validação / erros
        'validation_errors'=> 'Há erros de validação',
        'error_title'      => 'Erro',

        // Mensagens (flash)
        'created_success'      => 'Pergunta criada com sucesso.',
        'updated_success'      => 'Pergunta atualizada com sucesso.',
        'deleted_success'      => 'Pergunta excluída com sucesso.',
        'activated_success'    => 'Pergunta ativada com sucesso.',
        'deactivated_success'  => 'Pergunta desativada com sucesso.',
        'unexpected_error'     => 'Ocorreu um erro inesperado.',
    ],

    // =========================================================
    // ==== TRANSLATIONS =======================================
    // =========================================================
    'translations' => [
        // Títulos / textos gerais
        'title'                 => 'Gestão de traduções',
        'index_title'           => 'Gestão de traduções',
        'select_entity_title'   => 'Selecione :entity para traduzir',
        'edit_title'            => 'Editar tradução',
        'main_information'      => 'Informações principais',
        'ok'                    => 'OK',
        'save'                  => 'Salvar',
        'validation_errors'     => 'Há erros de validação',
        'updated_success'       => 'Tradução atualizada com sucesso.',
        'unexpected_error'      => 'Não foi possível atualizar a tradução.',

        // Seletor de idioma
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

        // Listagens / botões
        'select'                => 'Selecionar',
        'id_unavailable'        => 'ID indisponível',
        'no_items'              => 'Não há :entity disponíveis para traduzir.',

        // Campos comuns
        'name'                  => 'Nome',
        'description'           => 'Descrição',
        'content'               => 'Conteúdo',
        'overview'              => 'Resumo',
        'itinerary'             => 'Itinerário',
        'itinerary_name'        => 'Nome do itinerário',
        'itinerary_description' => 'Descrição do itinerário',
        'itinerary_items'       => 'Itens do itinerário',
        'item'                  => 'Item',
        'item_title'            => 'Título do item',
        'item_description'      => 'Descrição do item',
        'sections'              => 'Seções',
        'edit'                  => 'Editar',
        'close'                 => 'Fechar',
        'actions'               => 'Ações',

        // === Rótulos modulares ================================
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

            // Itinerário / itens
            'itinerary'             => 'Itinerário',
            'itinerary_name'        => 'Nome do itinerário',
            'itinerary_description' => 'Descrição do itinerário',
            'item'                  => 'Item',
            'item_title'            => 'Título do item',
            'item_description'      => 'Descrição do item',
        ],

        // === Overrides por entidade (opcional) ===============
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

        // Nomes de entidades (plural)
        'entities' => [
            'tours'            => 'Tours',
            'itineraries'      => 'Itinerários',
            'itinerary_items'  => 'Itens do itinerário',
            'amenities'        => 'Amenidades',
            'faqs'             => 'Perguntas frequentes',
            'policies'         => 'Políticas',
            'tour_types'       => 'Tipos de tour',
        ],

        // Nomes de entidades (singular)
        'entities_singular' => [
            'tours'            => 'tour',
            'itineraries'      => 'itinerário',
            'itinerary_items'  => 'item do itinerário',
            'amenities'        => 'amenidade',
            'faqs'             => 'pergunta frequente',
            'policies'         => 'política',
            'tour_types'       => 'tipo de tour',
        ],
    ],

    // =========================================================
    // ==== PROMOCODE ==========================================
    // =========================================================
    'promocode' => [
        'title'         => 'Códigos promocionais',
        'create_title'  => 'Gerar novo código promocional',
        'list_title'    => 'Códigos promocionais existentes',

        'success_title' => 'Sucesso',
        'error_title'   => 'Erro',

        'fields' => [
            'code'     => 'Código',
            'discount' => 'Desconto',
            'type'     => 'Tipo',
        ],

        'types' => [
            'percent'  => '%',
            'amount'   => '$',
        ],

        'symbols' => [
            'percent'  => '%',
            'currency' => '$',
        ],

        'table' => [
            'code'     => 'Código',
            'discount' => 'Desconto',
            'status'   => 'Status',
            'actions'  => 'Ações',
        ],

        'status' => [
            'used'       => 'Usado',
            'available'  => 'Disponível',
        ],

        'actions' => [
            'generate' => 'Gerar',
            'delete'   => 'Excluir',
        ],

        'confirm_delete' => 'Tem certeza de que deseja excluir este código?',
        'empty'          => 'Não há códigos promocionais disponíveis.',

        'messages' => [
    'created_success'       => 'Código promocional criado com sucesso.',
    'deleted_success'       => 'Código promocional excluído com sucesso.',
    'percent_over_100'      => 'A porcentagem não pode ser maior que 100.',
    'code_exists_normalized'=> 'Este código (ignorando espaços e maiúsculas) já existe.',
    'invalid_or_used'       => 'Código inválido ou já utilizado.',
    'valid'                 => 'Código válido.',
    'server_error'          => 'Erro no servidor, tente novamente.',
],
    ],

];
