<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Encryption;

abstract class DecodableInterceptor {
    /**
     * @var string
     */
    private $secret = null;

    public function __construct(string $secret) {
        if (!empty($secret)) {
            $this->secret = mb_substr(trim($secret), 0, 32);
        }
    }

    protected function decode(string $input): ?array {
        return Encryption::decrypt($this->secret, $input);
    }
}