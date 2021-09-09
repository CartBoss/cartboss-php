<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Resources\Coupon;
use CartBoss\Api\Utils;
use Delight\Cookie\Cookie;

class DiscountInterceptor
{
    const COOKIE_NAME = "cartboss_discount";
    const COOKIE_TTL = 60 * 60 * 24 * 365;
    const QUERY_VAR = "cb__discount";

    const STRUCT_KEY_CODE = 'code';
    const STRUCT_KEY_TYPE = 'type';
    const STRUCT_KEY_VALUE = 'value';

    private $cookie;
    private $secret_key;

    public function __construct($secret_key)
    {
        $this->secret_key = $secret_key;

        $cookie = new Cookie(self::COOKIE_NAME);
        $cookie->setMaxAge(self::COOKIE_TTL);
        $cookie->setSameSiteRestriction(null);
        $this->cookie = $cookie;

        $data = Utils::get_array_value($source ?? $_GET, self::QUERY_VAR, null);

        if (isset($data)) {
            $this->cookie->setValue($data);
            $this->cookie->saveAndSet();
        }
    }

    /**
     * @return Coupon|null
     */
    public function getCoupon(): ?Coupon
    {
        $cookie_data = $this->cookie::get(self::COOKIE_NAME, null);
        if (isset($cookie_data) && strlen($cookie_data) > 0) {
            $cookie_data = Utils::aes_decode($this->secret_key, $cookie_data);
            if (is_array($cookie_data)) {
                $coupon = new Coupon();
                $coupon->setCode(Utils::get_array_value($cookie_data, self::STRUCT_KEY_CODE));
                $coupon->setType(Utils::get_array_value($cookie_data, self::STRUCT_KEY_TYPE));
                $coupon->setValue(Utils::get_array_value($cookie_data, self::STRUCT_KEY_VALUE));
                return $coupon;
            }
        }
        return null;
    }

    public function clearCookieStorage()
    {
        $this->cookie->deleteAndUnset();
    }
}
