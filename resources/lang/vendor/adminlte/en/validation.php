<?php

return [
    'unique' => 'The :attribute has already been taken.',

    'custom' => [
        'email' => [
            'unique' => 'This email is already registered.',
        ],
        'password' => [
            'min' => 'The password must be at least :min characters.',
            'regex' => 'The password must include at least one number and one special character.',
            'confirmed' => 'The password confirmation does not match.',
        ],
    ],

    'attributes' => [
        'full_name' => 'full name',
        'email' => 'email',
        'phone' => 'phone',
        'password' => 'password',
        'password_confirmation' => 'password confirmation',
    ],
        'password_requirements' => [
        'length' => '- 8 characters long',
        'special' => '- 1 special character ( .¡!@#$%^&*()_+- )',
        'number' => '- 1 number',
    ],
                // --- Custom validation ---
    'validation_error_title' => 'Please correct the following errors:',
    'required_full_name' => 'Full name is required.',
    'required_email' => 'Email is required.',
    'required_password' => 'Password is required.',
    'required_password_confirmation' => 'Password confirmation is required.',
    'invalid_email' => 'The email address is invalid.',
    'email_already_taken' => 'This email is already registered.',
    'password_confirmation' => 'Password confirmation is required.',
   'invalid_credentials'=> 'Invalid credentials. Please try again.',

];
