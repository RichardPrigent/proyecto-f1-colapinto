<?php

namespace App\Contracts;

interface HttpClientInterface
{
    public function get(string $url);
}
