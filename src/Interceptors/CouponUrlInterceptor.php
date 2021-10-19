<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Resources\Contact;
use CartBoss\Api\Resources\Coupon;
use CartBoss\Api\Utils;

class CouponUrlInterceptor extends BaseUrlInterceptor
{
    const QUERY_VAR = "cb__discount";
    const COOKIE_NAME = "cbx_discount";
    const COOKIE_MAX_AGE = 60 * 60 * 24 * 30;

    const STRUCT_KEY_CODE = 'code';
    const STRUCT_KEY_TYPE = 'type';
    const STRUCT_KEY_VALUE = 'value';

    private $secret_key;

    public function __construct(string $api_key, string $attribution_token=null)
    {
        parent::__construct($attribution_token);

        $this->secret_key = $api_key;
    }

    /**
     * @return Contact|null
     */
    public function getCoupon(): ?Coupon
    {
        if (!empty($this->getValue())) {
            $data = Utils::aes_decode($this->secret_key, $this->getValue());

            if (is_array($data)) {
                $coupon = new Coupon();
                $coupon->setCode(Utils::get_array_value($data, self::STRUCT_KEY_CODE));
                $coupon->setType(Utils::get_array_value($data, self::STRUCT_KEY_TYPE));
                $coupon->setValue(Utils::get_array_value($data, self::STRUCT_KEY_VALUE));
                return $coupon;
            }
        }
        return null;
    }
}
