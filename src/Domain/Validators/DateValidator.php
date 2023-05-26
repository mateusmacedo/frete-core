<?php

declare(strict_types=1);

namespace Frete\Core\Domain\Validators;

use DateTime;
use Exception;

class DateValidator extends Validator
{
    public function validate(mixed $input): bool
    {
        return $this->isString($input) && $this->isAValidDate($input);
    }

    /**
     * @return null|array|string
     */
    public function getErrorMessage(): array|string|null
    {
        return 'Invalid date format';
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
