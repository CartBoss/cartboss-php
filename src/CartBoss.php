<?php

namespace CartBoss\Api;

use CartBoss\Api\Interceptors\AttributionInterceptor;
use CartBoss\Api\Interceptors\ContactInterceptor;
use CartBoss\Api\Interceptors\DiscountInterceptor;
use CartBoss\Api\Managers\ApiClient;
use CartBoss\Api\Resources\Contact;
use CartBoss\Api\Resources\Coupon;
use CartBoss\Api\Resources\Events\BaseEvent;
use CartBoss\Api\Resources\Events\OrderBaseEvent;
use Exception;
use Rakit\Validation\Validator;
use stdClass;

define('CARTBOSS_PATH', dirname(__FILE__));
define('CARTBOSS_VERSION', '1.0.0');

require 'vendor/autoload.php';

class CartBoss
{
    /**
     * @var DiscountInterceptor
     */
    private $discount_interceptor;
    /**
     * @var ContactInterceptor
     */
    private $contact_interceptor;
    /**
     * @var AttributionInterceptor
     */
    private $attribution_interceptor;
    /**
     * @var ApiClient
     */
    private $api_client;

    public function __construct($api_key)
    {
        $this->attribution_interceptor = new AttributionInterceptor();
        $this->discount_interceptor = new DiscountInterceptor($api_key);
        $this->contact_interceptor = new ContactInterceptor($api_key);
        $this->api_client = new ApiClient($api_key);
    }

    /**
     * @return string|null
     */
    public function getAttributionToken(): ?string
    {
        return $this->attribution_interceptor->getToken();
    }

    /**
     * @return Coupon|null
     */
    public function getCoupon(): ?Coupon
    {
        return $this->discount_interceptor->getCoupon();
    }

    /**
     * @return Contact|null
     */
    public function getContact(): ?Contact
    {
        return $this->contact_interceptor->getContact();
    }

    /**
     * @param BaseEvent $event
     * @return stdClass|null
     * @throws Exceptions\ApiException
     * @throws Exception
     */
    public function sendEvent(BaseEvent $event): ?stdClass
    {
        // validate event before sending
        $validator = new Validator();
        $validation = $validator->make($event->getPayload(), $event->getRules());
        $validation->validate();

        // simply throw exception with error messages
        if ($validation->fails()) {
            throw new Exception("Event '{$event->getEventName()}' has errors and won't be sent: " . print_r($validation->errors()->all(), true));
        }

        // send it to CartBoss
        return $this->api_client->performHttpCall(ApiClient::HTTP_POST, 'track', $this->api_client->parseRequestBody($event->getPayload()));
    }

    /**
     * @param OrderBaseEvent $event
     * @return stdClass|null
     * @throws Exceptions\ApiException
     * @throws Exception
     */
    public function sendOrderEvent(OrderBaseEvent $event): ?stdClass
    {
        return $this->sendEvent($event);
    }

    /**
     * @param string $order_id
     * @return stdClass|null
     * @throws Exceptions\ApiException
     */
    public function getOrder(string $order_id): ?stdClass
    {
        return $this->api_client->performHttpCall(ApiClient::HTTP_GET, "orders/{$order_id}");
    }
}