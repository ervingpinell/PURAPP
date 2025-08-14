<?php

return [
    'unique' => 'O :attribute já está em uso.',

    'custom' => [
        'email' => [
            'unique' => 'Este e-mail já está registrado.',
        ],
        'password' => [
            'min' => 'A senha deve ter pelo menos :min caracteres.',
            'regex' => 'A senha deve incluir ao menos um número e um caractere especial.',
            'confirmed' => 'A confirmação de senha não confere.',
        ],
    ],

    'attributes' => [
        'full_name' => 'nome completo',
        'email' => 'e-mail',
        'phone' => 'telefone',
        'password' => 'senha',
        'password_confirmation' => 'confirmação de senha',
    ],
            // --- Validação personalizada ---
    'validation_error_title' => 'Por favor, corrija os seguintes erros:',
    'required_full_name' => 'O nome completo é obrigatório.',
    'required_email' => 'O e-mail é obrigatório.',
    'required_password' => 'A senha é obrigatória.',
    'required_password_confirmation' => 'A confirmação da senha é obrigatória.',
    'invalid_email' => 'A endereço de e-mail é inválido.',
    'email_already_taken' => 'Este e-mail já está registrado.',
    'password_confirmation' => 'A confirmação da senha é obrigatória.',

   'password_requirements' => [
        'length' => '- Pelo menos 8 caracteres',
        'special' => '- 1 caractere especial ( .¡!@#$%^&*()_+- )',
        'number' => '- 1 número',
    ],

    'invalid_credentials'=> 'Credenciais inválidas. Por favor, tente novamente.',
];
