<?php

namespace CartBoss\Api\Interceptors;

use CartBoss\Api\Resources\Contact;
use CartBoss\Api\Utils;
use Delight\Cookie\Cookie;

class ContactInterceptor
{
    const COOKIE_NAME = "cartboss_contact";
    const COOKIE_TTL = 60 * 60 * 24 * 365;

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

    public function getContact(): ?Contact
    {
        $cookie_data = $this->cookie::get(self::COOKIE_NAME, null);
        if (isset($cookie_data) && strlen($cookie_data) > 0) {
            $cookie_data = Utils::aes_decode($this->secret_key, $cookie_data);

            if (is_array($cookie_data)) {
                $contact = new Contact();
                $contact->setPhone(Utils::get_array_value($cookie_data, self::STRUCT_KEY_PHONE));
                $contact->setEmail(Utils::get_array_value($cookie_data, self::STRUCT_KEY_EMAIL));
                $contact->setFirstName(Utils::get_array_value($cookie_data, self::STRUCT_KEY_FIRST_NAME));
                $contact->setLastName(Utils::get_array_value($cookie_data, self::STRUCT_KEY_LAST_NAME));
                $contact->setCompany(Utils::get_array_value($cookie_data, self::STRUCT_KEY_COMPANY));
                $contact->setAddress1(Utils::get_array_value($cookie_data, self::STRUCT_KEY_ADDRESS_1));
                $contact->setAddress2(Utils::get_array_value($cookie_data, self::STRUCT_KEY_ADDRESS_2));
                $contact->setCity(Utils::get_array_value($cookie_data, self::STRUCT_KEY_CITY));
                $contact->setState(Utils::get_array_value($cookie_data, self::STRUCT_KEY_STATE));
                $contact->setPostalCode(Utils::get_array_value($cookie_data, self::STRUCT_KEY_POSTAL_CODE));
                $contact->setCountry(Utils::get_array_value($cookie_data, self::STRUCT_KEY_COUNTRY));
                return $contact;
            }
        }
        return null;
    }

    public function clearCookieStorage()
    {
        $this->cookie->deleteAndUnset();
    }
}
