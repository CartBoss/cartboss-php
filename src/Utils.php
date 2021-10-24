<?php

namespace CartBoss\Api;

use Throwable;

class Utils {
    public static function getCurrentUrl(): string {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    public static function getCurrentHost(): string {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
    }

    public static function getUserAgent() {
        return self::getArrayValue($_SERVER, 'HTTP_USER_AGENT');
    }

    public static function getArrayValue($arr, $key, $default = null) {
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

    public static function getIp() {
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

    public static function getFirstNonEmpty() {
        foreach (func_get_args() as $arg) {
            if (isset($arg) && !empty($arg)) {
                return $arg;
            }
        }

        return null;
    }

    public static function getRandomString($length = 32): ?string {
        try {
            return bin2hex(random_bytes($length / 2));
        } catch (Throwable $e) {
            try {
                return bin2hex(openssl_random_pseudo_bytes($length / 2));
            } catch (Throwable $e) {
                return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
            }
        }
    }

    public static function getHash($val) {
        try {
            return hash('sha3-256', $val);
        } catch (Throwable $e) {
            return sha1($val);
        }
    }
}