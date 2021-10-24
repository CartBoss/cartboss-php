<?php

namespace CartBoss\Api;

class Encryption
{
    const CIPHER_ALGO = 'aes-256-cbc';

    const IV = 'iv';
    const CIPHERTEXT = 'ciphertext';

    public static function encrypt(string $secret, array $input): ?string
    {
        try {
            $ivlen = openssl_cipher_iv_length(self::CIPHER_ALGO);
            $iv = openssl_random_pseudo_bytes($ivlen);

            $json = json_encode($input);
            $encrypted = openssl_encrypt($json, self::CIPHER_ALGO, self::prepareSecret($secret), 0, $iv);

            return base64_encode(json_encode(array(
                self::IV => base64_encode($iv),
                self::CIPHERTEXT => $encrypted
            )));
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function decrypt(string $secret, ?string $input)
    {
        try {
            $input = base64_decode($input);
            $json = json_decode($input, true);
            $iv = base64_decode($json[self::IV]);
            $cipher = $json[self::CIPHERTEXT];

            $decrypted = openssl_decrypt($cipher, self::CIPHER_ALGO, self::prepareSecret($secret), 0, $iv);

            return json_decode($decrypted, true);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function prepareSecret($secret)
    {
        return mb_substr(trim($secret), 0, 32);
    }
}