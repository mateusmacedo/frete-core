<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

use DateTime;
use Exception;

class OptionalDateValidator extends Validator
{
    private bool $isValid = false;

    public function validate(mixed $input): bool
    {
        if (null === $input) {
            $this->isValid = true;
            return $this->isValid;
        }
        $this->isValid = (new DateValidator())->validate($input);
        return $this->isValid;
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return !$this->isValid ? 'Invalid date format' : null;
    }

    private function isString(mixed $input): bool
    {
        return is_string($input);
    }

    private function isAValidDate($date)
    {
        try {
            $date = new DateTime($date);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
