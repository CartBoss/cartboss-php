<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Resources\Contact;
use CartBoss\Api\Utils;

class ContactUrlInterceptor extends BaseUrlInterceptor
{
    const QUERY_VAR = "cb__contact";
    const COOKIE_NAME = "cb_contact";
    const COOKIE_MAX_AGE = 60 * 60 * 24 * 365;

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

    private $secret_key;

    public function __construct(string $api_key)
    {
        parent::__construct();

        $this->secret_key = $api_key;
    }

    /**
     * @return Contact|null
     */
    public function getContact(): ?Contact
    {
        if (!empty($this->getValue())) {
            $data = Utils::aes_decode($this->secret_key, $this->getValue());

            if (is_array($data)) {
                $contact = new Contact();
                $contact->setPhone(Utils::get_array_value($data, self::STRUCT_KEY_PHONE));
                $contact->setEmail(Utils::get_array_value($data, self::STRUCT_KEY_EMAIL));
                $contact->setFirstName(Utils::get_array_value($data, self::STRUCT_KEY_FIRST_NAME));
                $contact->setLastName(Utils::get_array_value($data, self::STRUCT_KEY_LAST_NAME));
                $contact->setCompany(Utils::get_array_value($data, self::STRUCT_KEY_COMPANY));
                $contact->setAddress1(Utils::get_array_value($data, self::STRUCT_KEY_ADDRESS_1));
                $contact->setAddress2(Utils::get_array_value($data, self::STRUCT_KEY_ADDRESS_2));
                $contact->setCity(Utils::get_array_value($data, self::STRUCT_KEY_CITY));
                $contact->setState(Utils::get_array_value($data, self::STRUCT_KEY_STATE));
                $contact->setPostalCode(Utils::get_array_value($data, self::STRUCT_KEY_POSTAL_CODE));
                $contact->setCountry(Utils::get_array_value($data, self::STRUCT_KEY_COUNTRY));

                return $contact;
            }
        }
        return null;
    }
}
