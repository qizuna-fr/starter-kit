<?php

declare(strict_types=1);

/** Qizuna 2025 - tous droits reservés  **/

namespace Infrastructure\Validators\PasswordComplexity;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PasswordComplexityValidator extends ConstraintValidator{

    const LOWERCASE_REGEX = '/[a-zà-öù-ÿ]/u';
    const UPPERCASE_REGEX = '/[A-ZÀ-ÖÙ-Ÿ]/u';
    const DIGITS_REGEX = '/\d/';



    public function validate($value, Constraint $constraint): void
    {



        if (null === $value || '' === $value) {
            return; // Laisse la contrainte NotBlank gérer le champ vide
        }

        if (mb_strlen($value) < 8) {
            $this->context->buildViolation($constraint->minLengthMessage)->addViolation();
        }

        if (!preg_match(self::LOWERCASE_REGEX, $value)) {
            $this->context->buildViolation($constraint->lowercaseMessage)->addViolation();
        }

        if (!preg_match(self::UPPERCASE_REGEX, $value)) {
            $this->context->buildViolation($constraint->uppercaseMessage)->addViolation();
        }

        if (!preg_match(self::DIGITS_REGEX, $value)) {
            $this->context->buildViolation($constraint->digitMessage)->addViolation();
        }

        if (!preg_match('/[!@#\$%\^&\*\(\)\[\]\{\}<>=\-_~\.,;:\'\"\/\\\\|\?+]/u', $value)) {
            $this->context->buildViolation($constraint->specialMessage)->addViolation();
        }

        // Caractères valides strictement selon la regex complète
        $validCharsPattern = '/[A-Za-zÀ-ÖØ-öø-ÿ\d!@#\$%\^&\*\(\)\[\]\{\}<>=\-_~\.,;:\'"\/\\\\|\?+]/u';

        $invalidChars = [];

        foreach (preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY) as $char) {
            if (!preg_match($validCharsPattern, $char)) {
                $invalidChars[] = $char;
            }
        }

        if (!empty($invalidChars)) {
            $invalids = implode(' ', array_map(fn($c) => json_encode($c, JSON_UNESCAPED_UNICODE), array_unique($invalidChars)));

            $this->context->buildViolation($constraint->invalidCharMessage)
                ->setParameter('{{ chars }}', $invalids)
                ->addViolation();
        }
    }

}
