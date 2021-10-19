<?php

namespace CartBoss\Api;

use CartBoss\Api\Exceptions\ApiException;
use CartBoss\Api\Exceptions\EventValidationException;
use CartBoss\Api\Managers\ApiClient;
use CartBoss\Api\Managers\Session;
use CartBoss\Api\Resources\Events\BaseEvent;
use CartBoss\Api\Resources\Events\OrderBaseEvent;
use CartBoss\Api\Resources\Events\PurchaseEvent;
use Rakit\Validation\Validator;
use stdClass;

define('CARTBOSS_PATH', dirname(__FILE__));
define('CARTBOSS_VERSION', '2.0.0');

class CartBoss
{
    /**
     * @var ApiClient
     */
    private $api_client;
    /**
     * @var string
     */
    private $api_key;

    public function __construct(string $api_key)
    {
        $this->api_key = $api_key;
        $this->api_client = new ApiClient($api_key);
//        $this->session = new Session();
    }

    public function onAttributionTokenIntercepted($func)
    {
        $interceptor = new \CartBoss\Api\Interceptors\AttributionInterceptor();
        if ($interceptor->getToken()->isValid()) {
            $func($interceptor->getToken());
        }
    }
    public function onCouponIntercepted($func)
    {
        $interceptor = new \CartBoss\Api\Interceptors\CouponInterceptor($this->api_key);
        if ($interceptor->getCoupon()->isValid()) {
            $func($interceptor->getCoupon());
        }
    }

//    /**
//     * @return Session
//     */
//    public function getSession(): Session
//    {
//        return $this->session;
//    }

    /**
     * @param OrderBaseEvent $event
     * @return stdClass|null
     * @throws ApiException
     * @throws EventValidationException
     */
    public function sendOrderEvent(OrderBaseEvent $event): ?stdClass
    {
        return $this->sendEvent($event);
    }

    /**
     * @param BaseEvent $event
     * @return stdClass|null
     * @throws EventValidationException
     * @throws ApiException
     */
    public function sendEvent(BaseEvent $event): ?stdClass
    {
        // validate event before sending
        $validator = new Validator();
        $validation = $validator->make($event->getPayload(), $event->getRules());
        $validation->validate();

        // simply throw exception with error messages
        if ($validation->fails()) {
            throw new EventValidationException(print_r($validation->errors()->all(), true));
        }

        // send it to CartBoss
        return $this->api_client->performHttpCall(ApiClient::HTTP_POST, 'track', $this->api_client->parseRequestBody($event->getPayload()));
    }

    /**
     * @param string $order_id
     * @return stdClass|null
     * @throws ApiException
     */
    public function getOrder(string $order_id): ?stdClass
    {
        return $this->api_client->performHttpCall(ApiClient::HTTP_GET, "orders/{$order_id}");
    }
}