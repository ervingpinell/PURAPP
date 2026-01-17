<?php

return [
    'no_reviews' => 'No reviews available',

    'what_visitors_say' => 'What do our customers say?',
    'powered_by'        => 'Powered by',

    'generic' => [
        'our_tour' => 'our tour',
    ],

    // =========================
    // Common
    // =========================
    'common' => [
        'reviews'   => 'Reviews',
        'provider'  => 'Provider',
        'status'    => 'Status',
        'tour'      => 'Tour',
        'rating'    => 'Classificação',
        'title'     => 'Título',
        'body'      => 'Conteúdo',
        'author'    => 'Autor',
        'actions'   => 'Ações',
        'filter'    => 'Filtrar',
        'search'    => 'Pesquisar',
        'id'        => 'ID',
        'public'    => 'Público',
        'private'   => 'Privado',
        'back'      => 'Voltar',
        'save'      => 'Salvar',
        'create'    => 'Criar',
        'edit'      => 'Editar',
        'delete'    => 'Excluir',
        'publish'   => 'Publicar',
        'hide'      => 'Ocultar',
        'flag'      => 'Sinalizar',
        'unflag'    => 'Remover sinalização',
        'apply'     => 'Aplicar',
        'yes'       => 'Sim',
        'no'        => 'Não',
        'not_found' => 'No results were found.',
        'clear'     => 'Clear',
        'language'  => 'Idioma',

        // 🔹 Keys added for Providers panel
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
        'deactivate_text' => 'O fornecedor deixará de estar ativo.',
        'cancel'          => 'Cancelar',
        'test'            => 'Testar conexão',
        'flush_cache'     => 'Limpar cache',
        'delete_confirm'  => 'Excluir fornecedor?',
        'system_locked'   => 'Fornecedor do sistema (bloqueado)',
    ],

    // =========================
    // Review statuses (moderation)
    // =========================
    'status' => [
        'pending'   => 'pending',
        'published' => 'published',
        'hidden'    => 'hidden',
        'flagged'   => 'flagged',
    ],

    // =========================
    // Admin - list / moderation
    // =========================
    'admin' => [
        'index_title'   => 'Reviews',
        'index_titel'   => 'Reviews', // alias for common typo

        'new_local'     => 'Novo (local)',
        'bulk_apply'    => 'Aplicar à seleção',
        'external_provider_note' => 'Nota do fornecedor externo',

        'responded'     => 'Respondido?',
        'last_reply'    => 'Última:',

        'filters'       => [
            'provider'  => 'Fornecedor',
            'status'    => 'Status',
            'tour_id'   => 'ID do Tour',
            'stars'     => '⭐',
            'q'         => 'Pesquisar texto/autor…',
            'responded' => 'Respondido?',
        ],

        'table' => [
            'date'     => 'Data',
            'review'   => 'Avaliação',
            'client'   => 'Cliente',
            'tour'     => 'Tour',
        ],

        'messages' => [
            'created'        => 'Review created.',
            'updated'        => 'Review updated.',
            'deleted'        => 'Review deleted.',
            'published'      => 'Review published.',
            'hidden'         => 'Review hidden.',
            'flagged'        => 'Review flagged.',
            'unflagged'      => 'Review unflagged.',
            'bulk_published' => ':n reviews published.',
            'bulk_hidden'    => ':n reviews hidden.',
            'bulk_flagged'   => ':n reviews flagged.',
            'bulk_deleted'   => ':n reviews deleted.',
            'publish_min_rating' => 'Cannot publish because the rating (:rating★) is lower than the allowed minimum (:min★).',
            'bulk_published_partial' => ':ok reviews published. :skipped skipped because their rating was lower than :min★.',
        ],
    ],

    // =========================
    // Admin - replies
    // =========================
    'replies' => [
        'reply'            => 'Responder',
        'title_create'     => 'Reply — Review #:id',
        'label_body'       => 'Reply',
        'label_is_public'  => 'Public',
        'label_notify'     => 'Send email to customer',
        'notify_to'        => 'It will be sent to: :email',
        'warn_no_email'    => 'Aviso: não encontramos um endereço de e-mail para o cliente nesta avaliação. A resposta será salva, mas nenhum e-mail será enviado.',
        'saved_notified'   => 'Reply published and emailed to :email.',
        'saved_no_email'   => 'Reply published. No email was sent because no recipient was found.',
        'deleted'          => 'Reply deleted.',
        'visibility_ok'    => 'Visibility updated.',
        'thread_title'     => 'Conversation — Review #:id',
        'thread_empty'     => 'No replies.',
        'last_reply'       => 'Última:',
    ],

    // =========================
    // Admin - post-purchase review requests
    // =========================
    'requests' => [
        'index_title' => 'Solicitar avaliações',
        'subtitle'    => 'Enviar links de avaliação pós-compra e gerenciar solicitações já enviadas.',

        // Tabs
        'tabs' => [
            'eligible'  => 'Elegíveis (reservas)',
            'requested' => 'Solicitadas (enviadas)',
        ],

        // Filters
        'filters' => [
            'q_placeholder' => 'ID, nome ou e-mail',
            'any_status'    => '— Qualquer —',
            'from'          => 'De',
            'to'            => 'Até',
        ],

        'window_days'      => 'Janela (dias)',
        'date_column'      => 'Coluna de data',
        'date_options'     => [
            'created_at' => 'Reserva criada',
            'tour_date'  => 'Data do tour',
        ],
        'calculated_range' => 'Intervalo calculado',
        'tour_id'          => 'ID do Tour',
        'btn_request'      => 'Solicitar avaliação',
        'no_eligible'      => 'Nenhuma reserva elegível.',

        'table' => [
            'booking'   => 'Reserva',
            'reference' => 'Referência',
            'sent_at'   => 'Enviado em',
            'states'    => 'Estados',
        ],

        'labels' => [
            'expires_in_days' => 'Expiração (dias)',
            'expires_at'      => 'Expira em',
            'used_at'         => 'Usado em',
        ],

        'actions' => [
            'resend'         => 'Reenviar',
            'confirm_delete' => 'Excluir esta solicitação?',
        ],

        'status' => [
            'active'    => 'Ativo',
            'sent'      => 'Enviado',
            'reminded'  => 'Lembrado',
            'used'      => 'Usado',
            'expired'   => 'Expirado',
            'cancelled' => 'Cancelado',
        ],

        'status_labels' => [
            'created'   => 'criado',
            'sent'      => 'enviado',
            'reminded'  => 'lembrado',
            'fulfilled' => 'concluído',
            'expired'   => 'expirado',
            'cancelled' => 'cancelado',
            'active'    => 'ativo',
        ],

        'send_ok'   => 'Solicitação de avaliação enviada.',
        'resend_ok' => 'Solicitação reenviada.',
        'remind_ok' => 'Lembrete enviado.',
        'expire_ok' => 'Solicitação expirada.',
        'deleted'   => 'Solicitação excluída.',
        'none'      => 'Nenhuma solicitação.',

        'errors' => [
            'used'    => 'Esta solicitação já foi usada.',
            'expired' => 'Esta solicitação expirou.',
        ],
        'no_requests' => 'Nenhuma solicitação encontrada.',
    ],

    // =========================
    // Public (review form)
    // =========================
    'public' => [
        'form_title'   => 'Leave a review',
        'labels'       => [
            'rating'       => 'Rating',
            'title'        => 'Title (optional)',
            'body'         => 'Your experience',
            'author_name'  => 'Your name (optional)',
            'author_email' => 'Your email (optional)',
            'submit'       => 'Submit review',
        ],
        'thanks'       => 'Thank you for your review! 🌿',
        'thanks_body'  => 'Your opinion is very important and helps us improve. We truly appreciate it.',
        'thanks_farewell' => "We hope you enjoyed your time with us and we hope to see you again soon.\n\n🇨🇷 Pura Vida mae! 🇨🇷",
        'thanks_dup'   => 'Thank you! We already had your review on file 🙌',
        'expired'      => 'This link has expired, but thank you so much for your intention 💚',
        'used'         => 'This request has already been used.',
        'used_help'    => 'This review link has already been used. If you think this is an error or want to update your comment, contact us and we will gladly help you.',
        'not_found'    => 'Request not found.',
        'back_home'    => 'Go back',
    ],

    // =========================
    // Emails
    // =========================
    'emails' => [

        'brand_from'   => config('app.name', 'Green Vacations CR'),
        'contact_line' => 'If you need help, contact us at :email or :phone. Visit us at :url.',
        'request' => [
            'preheader_with_date' => 'Tell us about your experience on :tour (:date). It only takes a minute.',
            'preheader'           => 'Tell us about your experience on :tour. It only takes a minute.',
            'subject'   => 'How was your experience on :tour?',
            'cta'       => 'Leave my review',
            'footer'    => 'Thank you for supporting local tourism. We hope to see you back soon! 🌿',
            'expires'   => '* This link will be active until: :date.',
            'greeting'  => 'Hi :name,',
            'intro'     => 'Pura vida! 🙌 Thank you for choosing us. We would love to know how your experience on :tour was.',
            'ask'       => 'Would you give us 1–2 minutes to leave your review? It really means a lot.',
            'fallback'  => 'If the button does not work, copy and paste this link into your browser:',
        ],
        'reply' => [
            'subject'  => 'Reply to your review',
            'greeting' => 'Hi :name,',
            'intro'    => 'Our team has replied to your review :extra.',
            'quote'    => '“:text”',
            'sign'     => '— :admin',
            'closing'  => 'Se tiver alguma dúvida ou quiser expandir seu comentário, basta responder a este e-mail. Pura vida! 🌿',
        ],
        'submitted' => [
            'subject' => 'New review received',
        ],
    ],

    // =========================
    // Front
    // =========================
    'front' => [
        'see_more'   => 'See more reviews',
        'no_reviews' => 'There are no reviews yet.',
    ],

    // =========================
    // Providers
    // =========================
    'providers' => [
        'index_title' => 'Provedores de avaliações',
        'indexable' => 'Indexável',
        'cache_ttl' => 'TTL Cache (seg)',
        'back' => 'Voltar',
        'actions' => 'Ações',
        'system_locked' => 'Provedor de Sistema',
        'messages' => [
            'cannot_delete_local' => 'O provedor “local” é um provedor de sistema e não pode ser excluído.',
            'created'        => 'Provedor criado.',
            'updated'        => 'Provedor atualizado.',
            'deleted'        => 'Provedor excluído.',
            'status_updated' => 'Status atualizado.',
            'cache_flushed'  => 'Cache limpo.',
            'test_fetched'   => ':n avaliações obtidas.',
            'mapping_added'   => 'Mapeamento adicionado com sucesso.',
            'mapping_updated' => 'Mapeamento atualizado com sucesso.',
            'mapping_deleted' => 'Mapeamento excluído com sucesso.',
        ],
        'product_map' => [
            'title' => 'Mapeamento de Produtos - :provider',
        ],
        'product_mapping_title' => 'Mapeamento de Produtos - :name',
        'product_mappings' => 'Mapeamentos de Produtos',
        'tour' => 'Tour',
        'select_tour' => 'Selecionar tour',
        'select_tour_placeholder' => 'Selecione um tour...',
        'product_code' => 'Código do produto',
        'product_code_placeholder' => 'Ex: 12732-ABC',
        'add_mapping' => 'Adicionar mapeamento',
        'no_mappings' => 'Nenhum mapeamento configurado',
        'confirm_delete_mapping' => 'Tem certeza que deseja excluir este mapeamento?',
        'help_title' => 'Ajuda',
        'help_text' => 'Mapeie códigos de produtos externos para tours internos para sincronizar avaliações corretamente.',
        'help_step_1' => 'Selecione um tour da lista',
        'help_step_2' => 'Insira o código do produto do provedor externo',
        'help_step_3' => 'Clique em "Adicionar" para criar o mapeamento',
    ],

    // =========================
    // Sync
    // =========================
    'sync' => [
        'queued' => 'Sync queued for :target.',
        'all'    => 'all providers',
    ],

    // =========================
    // Thread / conversation
    // =========================
    'thread' => [
        'title'             => 'Thread de avaliação #:id',
        'header'            => 'Thread — Avaliação #:id',
        'replies_header'    => 'Respostas',
        'th_date'           => 'Data',
        'th_admin'          => 'Admin',
        'th_visible'        => 'Visível',
        'th_body'           => 'Conteúdo',
        'th_actions'        => 'Ações',
        'toggle_visibility' => 'Alternar visibilidade',
        'delete'            => 'Excluir',
        'confirm_delete'    => 'Excluir resposta?',
        'empty'             => 'Ainda não há respostas.',
    ],

    // =========================
    // Admin form (create/edit)
    // =========================
    'form' => [
        'title_edit'       => 'Edit review',
        'title_new'        => 'Criar avaliação',
        'visible_publicly' => 'Visible publicly',
    ],

    // =========================
    // Alias for reply emails (if used outside "emails")
    // =========================
    'reply' => [
        'subject'          => 'Reply to your review',
        'greeting'         => 'Hi :name,',
        'about_html'       => 'about <strong>:tour</strong>',
        'about_text'       => 'about :tour',
        'intro'            => 'Our team has replied to your review :extra.',
        'quote'            => '“:text”',
        'sign'             => '— :admin',
        'closing'          => 'If you have any questions or would like to expand on your comment, just reply to this email. Pura vida! 🌿',
        'rights_reserved'  => 'All rights reserved',
    ],

    // Fallback for greeting if there is no name
    'traveler' => 'traveler',

    // =====================================================================
    // ==== Compatibility with old translation file (legacy) ================
    // =====================================================================

    'loaded'           => 'Reviews loaded successfully.',
    'provider_error'   => 'There was a problem with the review provider.',
    'service_busy'     => 'The service is busy, please try again shortly.',
    'unexpected_error' => 'An unexpected error occurred while loading reviews.',
    'anonymous'        => 'Anonymous',

    'what_customers_think_about' => 'What customers think about',
    'previous_review'            => 'Previous review',
    'next_review'                => 'Next review',
    'loading'                    => 'Loading reviews...',
    // 'what_visitors_say' already exists above; kept for compatibility
    'reviews_title'              => 'Customer reviews',
    // 'powered_by' already exists above; kept for compatibility
    'view_on_viator'             => 'View :name on Viator',

    // Modal / actions (legacy)
    'open_tour_title'    => 'Open tour page?',
    'open_tour_text_pre' => 'You are about to open the tour page for',
    'open_tour_confirm'  => 'Open now',
    'open_tour_cancel'   => 'Cancel',

    // Carousel controls (legacy, alias of front.see_more/less)
    'previous' => 'Previous',
    'next'     => 'Next',
    'see_more' => 'See more',
    'see_less' => 'See less',
];
