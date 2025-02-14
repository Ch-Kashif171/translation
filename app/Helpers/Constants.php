<?php

namespace App\Helpers;

class Constants
{

    const TRANSLATION = [
        'en' => [
            'welcome.message' => 'Welcome back, I am glad too see you again',
            'auth.login' => 'Sign In',
            'buttons.submit' => 'Submit'
        ],
        'fr' => [
            'welcome.message' => 'La bienvenue ! Je suis ravi de vous revoir.',
            'auth.login' => 'Se Connecter',
            'buttons.submit' => 'Soumettre'
        ],
        'es' => [
            'welcome.message' => '¡Bienvenido de nuevo! Me alegra verte otra vez.',
            'auth.login' => 'Iniciar Sesión',
            'buttons.submit' => 'Enviar'
        ]
    ];

    const TAGS = [
        'web' => 'web',
        'mobile' => 'mobile',
        'desktop' => 'desktop',
    ];
}
