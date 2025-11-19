<?php

return [
    'no_reviews' => 'Nenhuma avaliação disponível',
    'what_visitors_say' => 'O que nossos clientes dizem?',
    'powered_by'        => 'Fornecido por',

    'generic' => [
        'our_tour' => 'nosso passeio',
    ],

    // =========================
    // Comum
    // =========================
    'common' => [
        'reviews'   => 'Avaliações',
        'provider'  => 'Fornecedor',
        'status'    => 'Status',
        'tour'      => 'Tour',
        'rating'    => 'Avaliação',
        'title'     => 'Título',
        'body'      => 'Conteúdo',
        'author'    => 'Autor',
        'actions'   => 'Ações',
        'filter'    => 'Filtrar',
        'search'    => 'Buscar',
        'id'        => 'ID',
        'public'    => 'Pública',
        'private'   => 'Privada',
        'back'      => 'Voltar',
        'save'      => 'Salvar',
        'create'    => 'Criar',
        'edit'      => 'Editar',
        'delete'    => 'Excluir',
        'publish'   => 'Publicar',
        'hide'      => 'Ocultar',
        'flag'      => 'Marcar',
        'unflag'    => 'Desmarcar',
        'apply'     => 'Aplicar',
        'yes'       => 'Sim',
        'no'        => 'Não',
        'not_found' => 'Nenhum resultado encontrado.',
        'clear'     => 'Limpar',
        'language'  => 'Idioma',

        'new'             => 'Novo',
        'name'            => 'Nome',
        'active'          => 'Ativo',
        'inactive'        => 'Inativo',
        'indexable'       => 'Indexável',
        'indexable_yes'   => 'Inclui marcação indexável/JSON-LD',
        'indexable_no'    => 'Não indexável',
        'activate'        => 'Ativar fornecedor',
        'deactivate'      => 'Desativar fornecedor',
        'activate_title'  => 'Ativar fornecedor?',
        'activate_text'   => 'O fornecedor será ativado.',
        'deactivate_title' => 'Desativar fornecedor?',
        'deactivate_text' => 'O fornecedor será desativado.',
        'cancel'          => 'Cancelar',
        'test'            => 'Testar conexão',
        'flush_cache'     => 'Limpar cache',
        'delete_confirm'  => 'Excluir fornecedor?',
        'system_locked'   => 'Fornecedor do sistema (bloqueado)',
    ],

    // =========================
    // Status da avaliação
    // =========================
    'status' => [
        'pending'   => 'pendente',
        'published' => 'publicada',
        'hidden'    => 'oculta',
        'flagged'   => 'marcada',
    ],

    // =========================
    // Admin - Lista / moderação
    // =========================
    'admin' => [
        'index_title'   => 'Avaliações',
        'index_titel'   => 'Avaliações',

        'new_local'     => 'Nova (local)',
        'bulk_apply'    => 'Aplicar aos selecionados',

        'responded'     => 'Respondido?',
        'last_reply'    => 'Última:',

        'filters' => [
            'provider'  => 'Fornecedor',
            'status'    => 'Status',
            'tour_id'   => 'ID do tour',
            'stars'     => '⭐',
            'q'         => 'Buscar texto/autor...',
            'responded' => 'Respondido?',
        ],

        'table' => [
            'date'     => 'Data',
            'review'   => 'Avaliação',
            'client'   => 'Cliente',
            'tour'     => 'Tour',
        ],

        'messages' => [
            'created'        => 'Avaliação criada.',
            'updated'        => 'Avaliação atualizada.',
            'deleted'        => 'Avaliação excluída.',
            'published'      => 'Avaliação publicada.',
            'hidden'         => 'Avaliação oculta.',
            'flagged'        => 'Avaliação marcada.',
            'unflagged'      => 'Marcação removida.',
            'bulk_published' => ':n avaliações publicadas.',
            'bulk_hidden'    => ':n avaliações ocultas.',
            'bulk_flagged'   => ':n avaliações marcadas.',
            'bulk_deleted'   => ':n avaliações excluídas.',
            'publish_min_rating' => 'Não foi possível publicar: a nota (:rating★) é inferior ao mínimo permitido (:min★).',
            'bulk_published_partial' => ':ok avaliações publicadas. :skipped ignoradas por nota menor que :min★.',
        ],
    ],

    // =========================
    // Admin – Respostas
    // =========================
    'replies' => [
        'reply'            => 'Responder',
        'title_create'     => 'Responder — Avaliação #:id',
        'label_body'       => 'Resposta',
        'label_is_public'  => 'Pública',
        'label_notify'     => 'Enviar email ao cliente',
        'notify_to'        => 'Será enviado para: :email',
        'warn_no_email'    => 'Atenção: não encontramos email do cliente. A resposta será salva, mas nenhum email será enviado.',
        'saved_notified'   => 'Resposta publicada e enviada para :email.',
        'saved_no_email'   => 'Resposta publicada. Nenhum email enviado.',
        'deleted'          => 'Resposta excluída.',
        'visibility_ok'    => 'Visibilidade atualizada.',
        'thread_title'     => 'Conversação — Avaliação #:id',
        'thread_empty'     => 'Sem respostas.',
        'last_reply'       => 'Última:',
    ],

    // =========================
    // Admin – Solicitações pós-compra
    // =========================
    'requests' => [
        'index_title' => 'Solicitar avaliações',
        'subtitle'    => 'Envie links de avaliação pós-compra e gerencie as solicitações enviadas.',

        'tabs' => [
            'eligible'  => 'Elegíveis (reservas)',
            'requested' => 'Solicitadas (enviadas)',
        ],

        'filters' => [
            'q_placeholder' => 'ID, nome ou email',
            'any_status'    => '— Qualquer —',
            'from'          => 'De',
            'to'            => 'Até',
        ],

        'window_days'      => 'Janela (dias)',
        'date_column'      => 'Coluna de data',
        'calculated_range' => 'Intervalo calculado',
        'tour_id'          => 'ID do tour',
        'btn_request'      => 'Solicitar avaliação',
        'no_eligible'      => 'Nenhuma reserva elegível.',

        'table' => [
            'booking'   => 'Reserva',
            'reference' => 'Referência',
            'sent_at'   => 'Enviado em',
            'states'    => 'Estados',
        ],

        'labels' => [
            'expires_in_days' => 'Expira em (dias)',
            'expires_at'      => 'Expira em',
            'used_at'         => 'Usado em',
        ],

        'actions' => [
            'resend'         => 'Reenviar',
            'confirm_delete' => 'Excluir esta solicitação?',
        ],

        'status' => [
            'active'    => 'Ativas',
            'sent'      => 'Enviadas',
            'reminded'  => 'Reenviadas',
            'used'      => 'Usadas',
            'expired'   => 'Expiradas',
            'cancelled' => 'Canceladas',
        ],

        'status_labels' => [
            'created'   => 'criada',
            'sent'      => 'enviada',
            'reminded'  => 'reenviada',
            'fulfilled' => 'concluída',
            'expired'   => 'expirada',
            'cancelled' => 'cancelada',
            'active'    => 'ativa',
        ],

        'send_ok'   => 'Solicitação enviada.',
        'resend_ok' => 'Solicitação reenviada.',
        'remind_ok' => 'Lembrete enviado.',
        'expire_ok' => 'Solicitação expirada.',
        'deleted'   => 'Solicitação excluída.',
        'none'      => 'Nenhuma solicitação.',

        'errors' => [
            'used'    => 'Esta solicitação já foi usada.',
            'expired' => 'Esta solicitação está expirada.',
        ],
    ],

    // =========================
    // Público (formulário)
    // =========================
    'public' => [
        'form_title'   => 'Deixar uma avaliação',
        'labels'       => [
            'rating'       => 'Avaliação',
            'title'        => 'Título (opcional)',
            'body'         => 'Sua experiência',
            'author_name'  => 'Seu nome (opcional)',
            'author_email' => 'Seu email (opcional)',
            'submit'       => 'Enviar avaliação',
        ],
        'thanks'       => 'Obrigado pela sua avaliação! 🌿',
        'thanks_body'  => 'Sua opinião é muito importante e nos ajuda a melhorar. Agradecemos de coração!',
        'thanks_farewell' => "Esperamos que você tenha aproveitado muito e que possamos nos ver novamente em breve.\n\n🇨🇷 Pura Vida mae! 🇨🇷",
        'thanks_dup'   => 'Obrigado! Sua avaliação já havia sido registrada 🙌',
        'expired'      => 'Este link já expirou, mas agradecemos muito sua intenção 💚',
        'used'         => 'Esta solicitação já foi usada.',
        'used_help'    => 'Este link de avaliação já foi utilizado. Se acredita que é um erro ou deseja atualizar seu comentário, fale conosco.',
        'not_found'    => 'Solicitação não encontrada.',
        'back_home'    => 'Voltar',
    ],

    // =========================
    // Emails
    // =========================
    'emails' => [

        'brand_from'   => 'Green Vacations CR',
        'contact_line' => 'Se precisar de ajuda, entre em contato em :email ou :phone. Visite :url.',
        'request' => [
            'preheader_with_date' => 'Conte-nos sobre sua experiência em :tour (:date). Leva menos de um minuto.',
            'preheader'           => 'Conte-nos sobre sua experiência em :tour. Leva menos de um minuto.',
            'subject'   => 'Como foi sua experiência em :tour?',
            'cta'       => 'Deixar minha avaliação',
            'footer'    => 'Obrigado por apoiar o turismo local. Esperamos te ver novamente! 🌿',
            'expires'   => '* Este link ficará ativo até: :date.',
            'greeting'  => 'Olá :name,',
            'intro'     => 'Pura Vida! 🙌 Obrigado por nos escolher. Queremos saber como foi sua experiência em :tour.',
            'ask'       => 'Pode nos dedicar 1–2 minutinhos para deixar sua avaliação? Isso nos ajuda muito.',
            'fallback'  => 'Se o botão não funcionar, copie este link no navegador:',
        ],
        'reply' => [
            'subject'  => 'Resposta ao seu comentário',
            'greeting' => 'Olá :name,',
            'intro'    => 'Nossa equipe respondeu ao seu comentário :extra.',
            'quote'    => '“:text”',
            'sign'     => '— :admin',
        ],
        'submitted' => [
            'subject' => 'Nova avaliação recebida',
        ],
    ],

    // =========================
    // Front
    // =========================
    'front' => [
        'see_more'   => 'Ver mais avaliações',
        'no_reviews' => 'Ainda não há avaliações.',
    ],

    // =========================
    // Fornecedores
    // =========================
    'providers' => [
        'index_title' => 'Fornecedores de avaliações',
        'system_locked' => 'Fornecedor do sistema',
        'messages' => [
            'cannot_delete_local' => 'O fornecedor “local” é do sistema e não pode ser excluído.',
            'created'        => 'Fornecedor criado.',
            'updated'        => 'Fornecedor atualizado.',
            'deleted'        => 'Fornecedor excluído.',
            'status_updated' => 'Status atualizado.',
            'cache_flushed'  => 'Cache limpo.',
            'test_fetched'   => ':n avaliações obtidas.',
        ],
    ],

    // =========================
    // Sync
    // =========================
    'sync' => [
        'queued' => 'Sincronização enfileirada para :target.',
        'all'    => 'todos os fornecedores',
    ],

    // =========================
    // Thread / conversa
    // =========================
    'thread' => [
        'title'             => 'Thread da avaliação #:id',
        'header'            => 'Thread — Avaliação #:id',
        'replies_header'    => 'Respostas',
        'th_date'           => 'Data',
        'th_admin'          => 'Admin',
        'th_visible'        => 'Visível',
        'th_body'           => 'Conteúdo',
        'th_actions'        => 'Ações',
        'toggle_visibility' => 'Alterar visibilidade',
        'delete'            => 'Excluir',
        'confirm_delete'    => 'Excluir resposta?',
        'empty'             => 'Nenhuma resposta ainda.',
    ],

    // =========================
    // Formulário admin
    // =========================
    'form' => [
        'title_edit'       => 'Editar Avaliação',
        'title_new'        => 'Nova Avaliação',
        'visible_publicly' => 'Visível publicamente',
    ],

    // =========================
    // Alias de resposta
    // =========================
    'reply' => [
        'subject'          => 'Resposta ao seu comentário',
        'greeting'         => 'Olá :name,',
        'about_html'       => 'sobre <strong>:tour</strong>',
        'about_text'       => 'sobre :tour',
        'intro'            => 'Nossa equipe respondeu ao seu comentário :extra.',
        'quote'            => '“:text”',
        'sign'             => '— :admin',
        'closing'          => 'Se tiver dúvidas ou quiser complementar seu comentário, é só responder este email. Pura Vida! 🌿',
        'rights_reserved'  => 'Todos os direitos reservados',
    ],

    'traveler' => 'viajante',

    // =========================
    // Legacy / compatibilidade
    // =========================
    'loaded'           => 'Avaliações carregadas com sucesso.',
    'provider_error'   => 'Ocorreu um problema com o fornecedor de avaliações.',
    'service_busy'     => 'O serviço está ocupado. Tente novamente mais tarde.',
    'unexpected_error' => 'Ocorreu um erro inesperado ao carregar as avaliações.',
    'anonymous'        => 'Anônimo',

    'what_customers_think_about' => 'O que os clientes pensam sobre',
    'previous_review'            => 'Avaliação anterior',
    'next_review'                => 'Próxima avaliação',
    'loading'                    => 'Carregando avaliações...',
    'reviews_title'              => 'Avaliações de clientes',
    'view_on_viator'             => 'Ver :name no Viator',

    'open_tour_title'    => 'Abrir tour?',
    'open_tour_text_pre' => 'Você está prestes a abrir a página do tour',
    'open_tour_confirm'  => 'Abrir agora',
    'open_tour_cancel'   => 'Cancelar',

    'previous' => 'Anterior',
    'next'     => 'Próxima',
    'see_more' => 'Ver mais',
    'see_less' => 'Ver menos',
];
