<?php

declare(strict_types=1);

namespace Frete\Core\Domain;

abstract class Entity
{
    protected function __construct(private string|int|null $id = null)
    {
    }

    public function getId(): string|int|null
    {
        return $this->id;
    }

    public function setId(string|int $id): self
    {
        if (is_null($this->id)) {
            $this->id = $id;
        }
        return $this;
    }
}
