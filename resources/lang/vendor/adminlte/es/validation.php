<?php

return [
    'unique' => 'El :attribute ya está registrado.',

    'custom' => [
        'email' => [
            'unique' => 'Este correo ya está registrado.',
        ],
        'password' => [
            'min' => 'La contraseña debe tener al menos :min caracteres.',
            'regex' => 'La contraseña debe incluir al menos un número y un carácter especial.',
            'confirmed' => 'La confirmación de contraseña no coincide.',
        ],
    ],

    'attributes' => [
        'full_name' => 'Nombre completo',
        'email' => 'Correo electrónico',
        'phone' => 'Teléfono',
        'password' => 'Contraseña',
        'password_confirmation' => 'Confirmación de contraseña',
    ],
                // --- Validación personalizada ---
    'validation_error_title' => 'Por favor corrige los siguientes errores:',
    'required_full_name' => 'El nombre completo es obligatorio.',
    'required_email' => 'El correo electrónico es obligatorio.',
    'required_password' => 'La contraseña es obligatoria.',
    'required_password_confirmation' => 'La confirmación de contraseña es obligatoria.',
    'invalid_email' => 'La dirección de correo electrónico no es válida.',
    'email_already_taken' => 'Este correo electrónico ya está registrado.',
    'password_confirmation' => 'Se requiere confirmación de contraseña.',

    'password_requirements' => [
        'length' => '- Al menos 8 caracteres',
        'special' => '- 1 carácter especial ( .¡!@#$%^&*()_+- )',
        'number' => '- 1 número',
    ],
      'invalid_credentials'=> 'Credenciales inválidas. Por favor, intenta de nuevo.',

          'required' => 'El campo :attribute es obligatorio.',
    'unique'   => 'El campo :attribute ya está en uso.',
    'max' => [
        'string' => 'El campo :attribute no puede exceder :max caracteres.',
    ],

    'attributes' => [
        'name'        => 'nombre del itinerario',
        'description' => 'descripción',
    ],
];
