<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Resources\Coupon;
use CartBoss\Api\Utils;

class DiscountInterceptor
{
    const QUERY_VAR = "cb__discount";

    const STRUCT_KEY_CODE = 'code';
    const STRUCT_KEY_TYPE = 'type';
    const STRUCT_KEY_VALUE = 'value';

    private $secret_key;
    private $data_struct;

    public function __construct($secret_key)
    {
        $this->secret_key = $secret_key;
        $this->data_struct = Utils::get_array_value($_GET, self::QUERY_VAR, null);
    }

    /**
     * @return Coupon|null
     */
    public function getCoupon(): ?Coupon
    {
        if (!empty($this->data_struct)) {
            $data = Utils::aes_decode($this->secret_key, $this->data_struct);

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
