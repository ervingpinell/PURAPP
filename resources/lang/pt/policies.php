<?php

return [
    // Títulos / cabeçalhos
    'categories_title'        => 'Categorias de Políticas',
    'sections_title'          => 'Seções — :policy',

    // Colunas / campos comuns
    'id'                      => 'ID',
    'internal_name'           => 'Nome interno',
    'title_current_locale'    => 'Título (idioma atual)',
    'validity_range'          => 'Período de vigência',
    'valid_from'              => 'Válido a partir de',
    'valid_to'                => 'Válido até',
    'status'                  => 'Status',
    'sections'                => 'Seções',
    'actions'                 => 'Ações',
    'active'                  => 'Ativa',
    'inactive'                => 'Inativa',

    // Lista de categorias: ações
    'new_category'            => 'Nova categoria',
    'view_sections'           => 'Ver seções',
    'edit'                    => 'Editar',
    'activate_category'       => 'Ativar categoria',
    'deactivate_category'     => 'Desativar categoria',
    'delete'                  => 'Excluir',
    'delete_category_confirm' => 'Excluir a categoria e TODAS as suas seções?',
    'no_categories'           => 'Nenhuma categoria cadastrada.',
    'edit_category'           => 'Editar categoria',

    // Formulários (categoria)
    'title_label'             => 'Título',
    'description_label'       => 'Descrição',
    'register'                => 'Registrar',
    'save_changes'            => 'Salvar alterações',
    'close'                   => 'Fechar',

    // Seções
    'back_to_categories'      => 'Voltar às categorias',
    'new_section'             => 'Nova seção',
    'key'                     => 'Chave',
    'order'                   => 'Ordem',
    'activate_section'        => 'Ativar seção',
    'deactivate_section'      => 'Desativar seção',
    'delete_section_confirm'  => 'Excluir esta seção?',
    'no_sections'             => 'Nenhuma seção cadastrada.',
    'edit_section'            => 'Editar seção',
    'internal_key_optional'   => 'Chave interna (opcional)',
    'content_label'           => 'Conteúdo',

    // Público
    'page_title'              => 'Políticas',
    'no_policies'             => 'Não há políticas disponíveis no momento.',
    'section'                 => 'Seção',
    'cancellation_policy'     => 'Política de Cancelamento',
    'refund_policy'           => 'Política de Reembolso',
    'no_cancellation_policy'  => 'Nenhuma política de cancelamento configurada.',
    'no_refund_policy'        => 'Nenhuma política de reembolso configurada.',

    // Mensagens (categorias)
    'category_created'        => 'Categoria criada com sucesso.',
    'category_updated'        => 'Categoria atualizada com sucesso.',
    'category_activated'      => 'Categoria ativada com sucesso.',
    'category_deactivated'    => 'Categoria desativada com sucesso.',
    'category_deleted'        => 'Categoria excluída com sucesso.',

    // --- NOVAS CHAVES (refactor / utilidades do módulo) ---
    'untitled'                => 'Sem título',
    'no_content'              => 'Nenhum conteúdo disponível.',
    'display_name'            => 'Nome de exibição',
    'name'                    => 'Nome',
    'name_base'               => 'Nome base',
    'name_base_help'          => 'Identificador curto/slug da seção (somente interno).',
    'translation_content'     => 'Conteúdo traduzido',
    'locale'                  => 'Idioma',
    'save'                    => 'Salvar',
    'name_base_label'         => 'Nome base',
    'translation_name'        => 'Nome traduzido',
    'lang_autodetect_hint'    => 'Você pode escrever em qualquer idioma; a detecção é automática.',
    'bulk_edit_sections'      => 'Edição rápida de seções',
    'bulk_edit_hint'          => 'As alterações em todas as seções serão salvas junto com a tradução da categoria quando você clicar em “Salvar”.',
    'no_changes_made'         => 'Nenhuma alteração realizada.',
    'no_sections_found'       => 'Nenhuma seção encontrada.',

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

    // Confirmações específicas das seções
    'confirm_create_section'      => 'Criar esta seção?',
    'confirm_edit_section'        => 'Salvar as alterações da seção?',
    'confirm_delete_section'      => 'Tem certeza de que deseja excluir esta seção?',
    'confirm_deactivate_section'  => 'Tem certeza de que deseja desativar esta seção?',
    'confirm_activate_section'    => 'Tem certeza de que deseja ativar esta seção?',
];
