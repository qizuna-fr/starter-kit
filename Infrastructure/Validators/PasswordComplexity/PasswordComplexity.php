<?php

declare(strict_types=1);

/** Qizuna 2025 - tous droits reservés  **/

namespace Infrastructure\Validators\PasswordComplexity;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordComplexity extends Constraint
{
    public string $minLengthMessage = 'Le mot de passe doit contenir au moins 8 caractères.';
    public string $lowercaseMessage = 'Le mot de passe doit contenir au moins une lettre minuscule.';
    public string $uppercaseMessage = 'Le mot de passe doit contenir au moins une lettre majuscule.';
    public string $digitMessage     = 'Le mot de passe doit contenir au moins un chiffre.';
    public string $specialMessage   = 'Le mot de passe doit contenir au moins un caractère spécial autorisé.';
    public string $invalidCharMessage = 'Le mot de passe contient des caractères non autorisés : {{ chars }}.';

}
