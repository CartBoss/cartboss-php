<?php

namespace CartBoss\Api\Resources;

use Rakit\Validation\Validator;

class Attribution {
    const TOKEN = 'token';

    /**
     * @var string
     */
    private $token;

    public function __construct(string $token) {
        $this->token = trim($token);
    }

    public function getToken(): string {
        return $this->token;
    }

    public function isValid(): bool {
        $validator = new Validator;
        $validation = $validator->validate(array(self::TOKEN => $this->token), [
            self::TOKEN => 'required',
        ]);
        return !$validation->fails();
    }

    public function __toString() {
        return $this->token;
    }
}