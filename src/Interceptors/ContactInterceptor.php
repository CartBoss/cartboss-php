<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Resources\Contact;
use CartBoss\Api\Utils;

class ContactInterceptor
{
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

    private $secret_key;
    private $data_struct;

    public function __construct($secret_key)
    {
        $this->secret_key = $secret_key;
        $this->data_struct = Utils::get_array_value($_GET, self::QUERY_VAR, null);
    }

    /**
     * @return Contact|null
     */
    public function getContact(): ?Contact
    {
        if (!empty($this->data_struct)) {
            $data = Utils::aes_decode($this->secret_key, $this->data_struct);

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
