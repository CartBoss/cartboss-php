<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Resources\Coupon;
use CartBoss\Api\Utils;

class CouponInterceptor extends DecodeableInterceptor
{
    const QUERY_VAR = "cb__discount";

    const STRUCT_KEY_CODE = 'code';
    const STRUCT_KEY_TYPE = 'type';
    const STRUCT_KEY_VALUE = 'value';

    private $coupon;

    public function __construct(string $api_key)
    {
        parent::__construct($api_key);

        $this->coupon = new Coupon();

        $query_val = Utils::getArrayValue($_GET, static::QUERY_VAR, null);
        if (isset($query_val)) {
            $decoded_data = $this->decode($query_val);
            if (is_array($decoded_data)) {
                $this->coupon->setCode(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_CODE));
                $this->coupon->setType(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_TYPE));
                $this->coupon->setValue(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_VALUE));
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
