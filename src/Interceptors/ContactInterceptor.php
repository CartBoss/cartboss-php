<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Resources\Contact;
use CartBoss\Api\Utils;

class ContactInterceptor extends DecodableInterceptor {
    const QUERY_VAR = "cb__contact";

    const STRUCT_KEY_EMAIL = 'em';
    const STRUCT_KEY_PHONE = 'ph';
    const STRUCT_KEY_FIRST_NAME = 'fn';
    const STRUCT_KEY_LAST_NAME = 'ln';
    const STRUCT_KEY_ADDRESS_1 = 'a1';
    const STRUCT_KEY_ADDRESS_2 = 'a2';
    const STRUCT_KEY_COMPANY = 'cm';
    const STRUCT_KEY_POSTAL_CODE = 'pc';
    const STRUCT_KEY_STATE = 'st';
    const STRUCT_KEY_CITY = 'ct';
    const STRUCT_KEY_COUNTRY = 'cn';

    /**
     * @var Contact
     */
    private $contact;

    public function __construct(string $api_key) {
        parent::__construct($api_key);

        $this->contact = new Contact();

        $query_val = Utils::getArrayValue($_GET, static::QUERY_VAR, null);
        if (isset($query_val)) {
            $decoded_data = $this->decode($query_val);
            if (is_array($decoded_data)) {
                $this->contact->setPhone(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_PHONE));
                $this->contact->setEmail(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_EMAIL));
                $this->contact->setFirstName(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_FIRST_NAME));
                $this->contact->setLastName(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_LAST_NAME));
                $this->contact->setCompany(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_COMPANY));
                $this->contact->setAddress1(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_ADDRESS_1));
                $this->contact->setAddress2(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_ADDRESS_2));
                $this->contact->setCity(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_CITY));
                $this->contact->setState(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_STATE));
                $this->contact->setPostalCode(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_POSTAL_CODE));
                $this->contact->setCountry(Utils::getArrayValue($decoded_data, self::STRUCT_KEY_COUNTRY));
            }
        }
    }

    /**
     * @return Contact
     */
    public function getContact(): Contact {
        return $this->contact;
    }
}
