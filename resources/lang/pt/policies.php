<?php

return [

    // =========================================================
    // [00] GENÉRICO
    // =========================================================
    'page_title'  => 'Políticas',
    'no_policies' => 'Nenhuma política disponível no momento.',
    'no_sections' => 'Nenhuma seção disponível no momento.',

    // =========================================================
    // [01] CHECKOUT (usado por resources/views/policies/checkout/content.blade.php)
    // =========================================================
    'checkout' => [
        'card_title'  => 'Seu pedido',
        'details'     => 'Detalhe',
        'must_accept' => 'Você deve ler e aceitar todas as políticas para continuar com o pagamento.',
        'accept_label_html' =>
            'Li e aceito os <strong>Termos e Condições</strong>, a <strong>Política de Privacidade</strong> e todas as <strong>Políticas de Cancelamento, Devoluções e Garantia</strong>.',
        'back'       => 'Voltar',
        'pay'        => 'Processar pagamento',
        'order_full' => 'Detalhe completo do pedido',

        // Versões visíveis (opcional)
        'version' => [
            'terms'   => 'v1',
            'privacy' => 'v1',
        ],

        // Títulos de cada bloco
        'titles' => [
            'terms'        => 'Termos e Condições',
            'privacy'      => 'Política de Privacidade',
            'cancellation' => 'Política de Cancelamento',
            'refunds'      => 'Política de Devoluções',
            'warranty'     => 'Política de Garantia',
            'payments'     => 'Formas de Pagamento',
        ],

        // Conteúdos HTML por bloco
        'bodies' => [
        'terms_html' => <<<HTML
        <p>Estes termos regem a compra de passeios e serviços oferecidos pela Green Vacations CR.</p>
        <ul>
        <li><strong>Escopo:</strong> A compra se aplica exclusivamente aos serviços listados para as datas e horários selecionados.</li>
        <li><strong>Preços e taxas:</strong> Os preços são exibidos em USD e incluem impostos quando aplicável. Quaisquer cobranças adicionais serão informadas antes do pagamento.</li>
        <li><strong>Capacidade e disponibilidade:</strong> As reservas estão sujeitas à disponibilidade e às validações de capacidade.</li>
        <li><strong>Alterações:</strong> Mudanças de data/horário dependem de disponibilidade e podem gerar diferenças tarifárias.</li>
        <li><strong>Responsabilidade:</strong> Os serviços são prestados conforme a regulamentação costarriquenha aplicável.</li>
        </ul>
        HTML,
                'privacy_html' => <<<HTML
        <p>Tratamos dados pessoais de acordo com a legislação aplicável. Coletamos apenas os dados necessários para gerenciar reservas, pagamentos e a comunicação com o cliente.</p>
        <ul>
        <li><strong>Uso das informações:</strong> Gestão da compra, atendimento ao cliente, notificações operacionais e cumprimento legal.</li>
        <li><strong>Compartilhamento:</strong> Não vendemos nem comercializamos dados pessoais.</li>
        <li><strong>Direitos:</strong> Você pode exercer os direitos de acesso, retificação, oposição e exclusão por meio dos nossos canais de contato.</li>
        </ul>
        HTML,
                'cancellation_html' => <<<HTML
        <p>O cliente pode solicitar o cancelamento antes do início do serviço conforme os prazos abaixo:</p>
        <ul>
        <li>Até 2 horas antes: <strong>reembolso integral</strong>.</li>
        <li>Entre 2 horas e 1 hora antes: <strong>reembolso de 50%</strong>.</li>
        <li>Menos de 1 hora: <strong>sem reembolso</strong>.</li>
        </ul>
        <p>Os reembolsos são realizados no <strong>mesmo cartão</strong> utilizado na compra. O prazo de crédito depende do banco emissor.</p>
        <p>Informe seu <strong>número de pedido</strong> e <strong>nome completo</strong> ao solicitar o cancelamento. Os prazos podem variar por passeio, se indicado na página do produto.</p>
        HTML,
                'refunds_html' => <<<HTML
        <p>Quando aplicável, o reembolso é feito no <strong>mesmo cartão</strong> utilizado na compra. Os prazos dependem do emissor do meio de pagamento.</p>
        <p>Para solicitar reembolso: info@greenvacationscr.com / (+506) 2479 1471.</p>
        HTML,
                'warranty_html' => <<<HTML
        <p>Aplica-se a serviços não prestados ou prestados de forma substancialmente diferente do ofertado. O cliente tem <strong>7 dias</strong> para reportar ocorrências. A garantia aplica-se aos serviços turísticos comercializados pela Green Vacations CR.</p>
        HTML,
                'payments_html' => <<<HTML
        <p>O pagamento é realizado por meio de Link de Pagamento Alignet com cartões Visa/Mastercard/Amex habilitados para compras on-line.</p>
        HTML,
            ],
    ],

    // =========================================================
    // [02] CAMPOS (admin/listagens)
    // =========================================================
    'fields' => [
        'title'       => 'Título',
        'description' => 'Descrição',
        'type'        => 'Tipo',
        'is_active'   => 'Ativo',
    ],

    // =========================================================
    // [03] TIPOS
    // =========================================================
    'types' => [
        'cancellation' => 'Política de Cancelamento',
        'refund'       => 'Política de Reembolso',
        'terms'        => 'Termos e Condições',
    ],

    // =========================================================
    // [04] MENSAGENS
    // =========================================================
    'success' => [
        'created' => 'Política criada com sucesso.',
        'updated' => 'Política atualizada com sucesso.',
        'deleted' => 'Política excluída com sucesso.',
    ],

    'error' => [
        'create' => 'Não foi possível criar a política.',
        'update' => 'Não foi possível atualizar a política.',
        'delete' => 'Não foi possível excluir a política.',
    ],
];
