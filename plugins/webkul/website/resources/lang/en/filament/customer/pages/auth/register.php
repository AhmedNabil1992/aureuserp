<?php

return [
    'title'         => 'Register',
    'heading'       => 'Register',
    'notifications' => [
        'throttled' => [
            'title' => 'Too many attempts. Try again in :seconds seconds.',
            'body'  => 'Please wait :seconds seconds (:minutes minutes) before trying again.',
        ],
    ],
    'form' => [
        'name' => [
            'label' => 'Name',
        ],
        'email' => [
            'label' => 'Email address',
        ],
        'password' => [
            'label'                => 'Password',
            'validation_attribute' => 'password',
        ],
        'password_confirmation' => [
            'label' => 'Confirm password',
        ],
        'phone' => [
            'label' => 'Phone number',
        ],
        'country' => [
            'label' => 'Country',
        ],
        'state' => [
            'label' => 'State / Province',
        ],
        'city' => [
            'label' => 'City',
        ],
        'street' => [
            'label' => 'Street address',
        ],
        'actions' => [
            'register' => [
                'label' => 'Create account',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'before' => 'Already have an account?',
            'label'  => 'Log in',
        ],
    ],
];
