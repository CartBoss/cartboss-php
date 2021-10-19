<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Resources\Coupon;
use CartBoss\Api\Utils;

class CouponInterceptor
{
    const QUERY_VAR = "cb__discount";

    const STRUCT_KEY_CODE = 'code';
    const STRUCT_KEY_TYPE = 'type';
    const STRUCT_KEY_VALUE = 'value';

    private $secret_key;
    private $coupon;

    public function __construct(string $api_key)
    {
        $this->secret_key = $api_key;
        $this->coupon = new Coupon();

        $query_val = Utils::get_array_value($_GET, static::QUERY_VAR, null);
        if (isset($query_val)) {
            $decoded_data = Utils::aes_decode($this->secret_key, $query_val);
            if (is_array($decoded_data)) {
                $this->coupon->setCode(Utils::get_array_value($decoded_data, self::STRUCT_KEY_CODE));
                $this->coupon->setType(Utils::get_array_value($decoded_data, self::STRUCT_KEY_TYPE));
                $this->coupon->setValue(Utils::get_array_value($decoded_data, self::STRUCT_KEY_VALUE));
            }
        }
    }

    /**
     * @return Coupon
     */
    public function getCoupon(): Coupon
    {
        return $this->coupon;
    }
}
