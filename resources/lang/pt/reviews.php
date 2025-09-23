<?php

return [

    'what_visitors_say' => 'O que nossos clientes dizem?',
    'powered_by'        => 'Distribuído por',

    // =========================
    // Comum
    // =========================
    'common' => [
        'reviews'   => 'Avaliações',
        'provider'  => 'Provedor',
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
        'flag'      => 'Denunciar',
        'unflag'    => 'Remover denúncia',
        'apply'     => 'Aplicar',
        'yes'       => 'Sim',
        'no'        => 'Não',
        'not_found' => 'Nenhum resultado encontrado.',
        'clear'     => 'Limpar',
        'language'  => 'Idioma',

        // Adições para o painel de Provedores
        'new'              => 'Novo',
        'name'             => 'Nome',
        'active'           => 'Ativo',
        'inactive'         => 'Inativo',
        'indexable'        => 'Indexável',
        'indexable_yes'    => 'Inclui marcação indexável/JSON-LD',
        'indexable_no'     => 'Não indexável',
        'activate'         => 'Ativar provedor',
        'deactivate'       => 'Desativar provedor',
        'activate_title'   => 'Ativar provedor?',
        'activate_text'    => 'O provedor será ativado.',
        'deactivate_title' => 'Desativar provedor?',
        'deactivate_text'  => 'O provedor será desativado.',
        'cancel'           => 'Cancelar',
        'test'             => 'Testar conexão',
        'flush_cache'      => 'Limpar cache',
        'delete_confirm'   => 'Excluir provedor?',
        'system_locked'    => 'Provedor do sistema (bloqueado)',
    ],

    // =========================
    // Status da avaliação
    // =========================
    'status' => [
        'pending'   => 'pendente',
        'published' => 'publicada',
        'hidden'    => 'oculta',
        'flagged'   => 'sinalizada',
    ],

    // =========================
    // Admin – lista / moderação
    // =========================
    'admin' => [
        'index_title' => 'Avaliações',
        'index_titel' => 'Avaliações', // alias legacy

        'new_local'  => 'Nova (local)',
        'bulk_apply' => 'Aplicar aos selecionados',

        'responded'  => 'Respondido?',
        'last_reply' => 'Última:',

        'filters' => [
            'provider'  => 'Provedor',
            'status'    => 'Status',
            'tour_id'   => 'ID do tour',
            'stars'     => '⭐',
            'q'         => 'Buscar texto/autor...',
            'responded' => 'Respondido?',
        ],

        'table' => [
            'date'   => 'Data',
            'review' => 'Avaliação',
            'client' => 'Cliente',
            'tour'   => 'Tour',
        ],

        'messages' => [
            'created'        => 'Avaliação criada.',
            'updated'        => 'Avaliação atualizada.',
            'deleted'        => 'Avaliação excluída.',
            'published'      => 'Avaliação publicada.',
            'hidden'         => 'Avaliação oculta.',
            'flagged'        => 'Avaliação sinalizada.',
            'unflagged'      => 'Sinalização removida.',
            'bulk_published' => ':n avaliações publicadas.',
            'bulk_hidden'    => ':n avaliações ocultas.',
            'bulk_flagged'   => ':n avaliações sinalizadas.',
            'bulk_deleted'   => ':n avaliações excluídas.',
            'publish_min_rating' => 'Não é possível publicar porque a nota (:rating★) é menor que o mínimo permitido (:min★).',
            'bulk_published_partial' => ':ok avaliações publicadas. :skipped ignoradas por nota menor que :min★.',
        ],
    ],

    // =========================
    // Admin – respostas
    // =========================
    'replies' => [
        'reply'            => 'Responder',
        'title_create'     => 'Responder — Avaliação #:id',
        'label_body'       => 'Resposta',
        'label_is_public'  => 'Pública',
        'label_notify'     => 'Enviar e-mail ao cliente',
        'notify_to'        => 'Será enviado para: :email',
        'warn_no_email'    => 'Atenção: não encontramos e-mail do cliente nesta avaliação. A resposta será salva, mas nenhum e-mail será enviado.',
        'saved_notified'   => 'Resposta publicada e enviada para :email.',
        'saved_no_email'   => 'Resposta publicada. Nenhum e-mail enviado por falta de destinatário.',
        'deleted'          => 'Resposta excluída.',
        'visibility_ok'    => 'Visibilidade atualizada.',
        'thread_title'     => 'Tópico — Avaliação #:id',
        'thread_empty'     => 'Sem respostas.',
        'last_reply'       => 'Última:',
    ],

    // =========================
    // Admin – solicitações pós-compra
    // =========================
    'requests' => [
        'index_title' => 'Solicitar avaliações',
        'subtitle'    => 'Envie links de avaliação pós-compra e gerencie solicitações já enviadas.',

        'tabs' => [
            'eligible'  => 'Elegíveis (reservas)',
            'requested' => 'Solicitadas (enviadas)',
        ],

        'filters' => [
            'q_placeholder' => 'ID, nome ou e-mail',
            'any_status'    => '— Qualquer —',
            'from'          => 'De',
            'to'            => 'Até',
        ],

        'window_days'      => 'Janela (dias)',
        'date_column'      => 'Coluna de data',
        'calculated_range' => 'Intervalo calculado',
        'tour_id'          => 'ID do tour',
        'btn_request'      => 'Solicitar avaliação',
        'no_eligible'      => 'Não há reservas elegíveis.',

        'table' => [
            'booking'   => 'Reserva',
            'reference' => 'Referência',
            'sent_at'   => 'Enviado',
            'states'    => 'Estados',
        ],

        'labels' => [
            'expires_in_days' => 'Dias de expiração',
            'expires_at'      => 'Expira',
            'used_at'         => 'Usada',
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

        'send_ok'   => 'Solicitação de avaliação enviada.',
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
            'author_email' => 'Seu e-mail (opcional)',
            'submit'       => 'Enviar avaliação',
        ],
        'thanks'       => 'Obrigado pela sua avaliação! 🌿',
        'thanks_dup'   => 'Obrigado! Sua avaliação já estava registrada 🙌',
        'expired'      => 'Este link expirou, mas obrigado pela intenção 💚',
        'used'         => 'Esta solicitação já foi usada.',
        'used_help'    => 'Este link de avaliação já foi usado. Se você acredita que é um erro ou deseja atualizar seu comentário, entre em contato e teremos prazer em ajudar.',
        'not_found'    => 'Solicitação não encontrada.',
    ],

    // =========================
    // E-mails
    // =========================
    'emails' => [
        'brand_from'   => 'Green Vacations CR',
        'contact_line' => 'Se precisar de ajuda, fale conosco em :email ou :phone. Visite-nos em :url.',
        'request' => [
            'subject'   => 'Como foi sua experiência em :tour?',
            'cta'       => 'Deixar minha avaliação',
            'footer'    => 'Obrigado por apoiar o turismo local. Esperamos vê-lo novamente em breve! 🌿',
            'expires'   => '* Este link ficará ativo até: :date.',
            'greeting'  => 'Olá :name,',
            'intro'     => 'Pura vida! 🙌 Obrigado por nos escolher. Queremos saber como foi em :tour.',
            'ask'       => 'Você pode dedicar 1–2 minutos para deixar sua avaliação? Ajuda demais.',
            'fallback'  => 'Se o botão não funcionar, copie e cole este link no seu navegador:',
        ],
        'reply' => [
            'subject'  => 'Resposta à sua avaliação',
            'greeting' => 'Olá :name,',
            'intro'    => 'Nossa equipe respondeu à sua avaliação: :extra.',
            'quote'    => '“:text”',
            'sign'     => '— :admin',
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
    // Provedores
    // =========================
    'providers' => [
        'index_title' => 'Provedores de avaliações',
        'system_locked' => 'Provedor do sistema',
        'messages' => [
            'cannot_delete_local' => 'O provedor “local” é um registro do sistema e não pode ser excluído.',
            'created'        => 'Provedor criado.',
            'updated'        => 'Provedor atualizado.',
            'deleted'        => 'Provedor excluído.',
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
        'all'    => 'todos os provedores',
    ],

    // =========================
    // Tópico / conversa
    // =========================
    'thread' => [
        'title'             => 'Tópico da avaliação #:id',
        'header'            => 'Tópico — Avaliação #:id',
        'replies_header'    => 'Respostas',
        'th_date'           => 'Data',
        'th_admin'          => 'Admin',
        'th_visible'        => 'Visível',
        'th_body'           => 'Conteúdo',
        'th_actions'        => 'Ações',
        'toggle_visibility' => 'Alterar visibilidade',
        'delete'            => 'Excluir',
        'confirm_delete'    => 'Excluir resposta?',
        'empty'             => 'Ainda sem respostas.',
    ],

    // =========================
    // Formulário admin (criar/editar)
    // =========================
    'form' => [
        'title_edit'       => 'Editar Avaliação',
        'title_new'        => 'Nova Avaliação',
        'visible_publicly' => 'Visível publicamente',
    ],

    // =========================
    // Alias de e-mail de resposta
    // =========================
    'reply' => [
        'subject'          => 'Resposta à sua avaliação',
        'greeting'         => 'Olá :name,',
        'about_html'       => 'sobre <strong>:tour</strong>',
        'about_text'       => 'sobre :tour',
        'intro'            => 'Nossa equipe respondeu à sua avaliação: :extra.',
        'quote'            => '“:text”',
        'sign'             => '— :admin',
        'closing'          => 'Se tiver dúvidas ou quiser ampliar seu comentário, basta responder a este e-mail. Pura vida! 🌿',
        'rights_reserved'  => 'Todos os direitos reservados',
    ],

    'traveler' => 'viajante',

    // =========================
    // Compatibilidade legacy
    // =========================
    'loaded'           => 'Avaliações carregadas com sucesso.',
    'provider_error'   => 'Houve um problema com o provedor de avaliações.',
    'service_busy'     => 'O serviço está ocupado. Tente novamente em instantes.',
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
    'next'     => 'Próximo',
    'see_more' => 'Ver mais',
    'see_less' => 'Ver menos',
];
