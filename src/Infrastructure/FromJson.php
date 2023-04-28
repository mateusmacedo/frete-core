<?php

declare(strict_types=1);

namespace Frete\Core\Infrastructure;

interface FromJson
{
    public static function fromJson(string $json): self;
}
