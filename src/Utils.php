<?php

namespace CartBoss\Api;

use Exception;

class Utils
{
    public static function get_current_url()
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public static function get_user_agent()
    {
        return self::get_array_value($_SERVER, 'HTTP_USER_AGENT');
    }

    public static function get_array_value($arr, $key, $default = null)
    {
        if (!is_array($arr)) {
            return $default;
        }
        if (!array_key_exists($key, $arr)) {
            return $default;
        }
        if (empty($arr[$key])) {
            return $default;
        }
        return $arr[$key];
    }

    public static function get_visitor_ip()
    {
        foreach (
            array(
                'HTTP_CF_CONNECTING_IP',
                'TRUE_CLIENT_IP',
                'HTTP_CLIENT_IP',
                'HTTP_X_REAL_IP',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_CLUSTER_CLIENT_IP',
                'HTTP_FORWARDED_FOR',
                'HTTP_FORWARDED',
                'REMOTE_ADDR'
            ) as $key
        ) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return null;
    }

    public static function get_first_non_empty_value()
    {
        foreach (func_get_args() as $arg) {
            if (isset($arg) && !empty($arg)) {
                return $arg;
            }
        }

        return null;
    }

    public static function is_true($val)
    {
        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    public static function aes_decode($secret, $input)
    {
        try {
            $secret = mb_substr($secret, 0, 32);
            $input = base64_decode($input);
            $json = json_decode($input);
            $iv = base64_decode($json->iv);
            $decrypted = openssl_decrypt($json->ciphertext, 'aes-256-cbc', $secret, 0, $iv);

            return json_decode($decrypted, true);
        } catch (Exception $e) {
            return null;
        }
    }

    public static function aes_encode($secret, $input)
    {
        try {
            $ivlen = openssl_cipher_iv_length('aes-256-cbc');
            $iv = openssl_random_pseudo_bytes($ivlen);
            $secret = mb_substr($secret, 0, 32);
            $encrypted = openssl_encrypt($input, 'aes-256-cbc', $secret, 0, $iv);
            return array(
                'iv'=>$iv,
                'chipher'=>$encrypted
                );

//            return json_decode($decrypted, true);
        } catch (Exception $e) {
            return null;
        }
    }
}