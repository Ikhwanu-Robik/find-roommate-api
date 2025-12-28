<?php

namespace Tests\Util;

interface IDataProvider
{
    public function toArray(): array;
    
    public function exclude(array $keys): static;

    public function replace(array $data): static;

}