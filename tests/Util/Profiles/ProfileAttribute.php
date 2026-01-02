<?php

namespace Tests\Util\Profiles;

use Tests\Util\IDataProvider;

class ProfileAttribute implements IDataProvider
{
    public function toArray(): array
    {
        return [];
    }

    public function exclude(array $keys): static
    {
        return $this;
    }

    public function replace(array $data): static
    {
        return $this;
    }
}
