<?php

namespace CartBoss\Api;

use CartBoss\Api\Exceptions\ApiException;
use CartBoss\Api\Exceptions\EventValidationException;
use CartBoss\Api\Interceptors\AttributionInterceptor;
use CartBoss\Api\Interceptors\ContactInterceptor;
use CartBoss\Api\Interceptors\CouponInterceptor;
use CartBoss\Api\Managers\ApiClient;
use CartBoss\Api\Managers\Session;
use CartBoss\Api\Resources\Events\BaseEvent;
use CartBoss\Api\Resources\Events\OrderBaseEvent;
use Rakit\Validation\Validator;
use stdClass;

define('CARTBOSS_PATH', dirname(__FILE__));
define('CARTBOSS_VERSION', '2.0.0');

class CartBoss
{
    const ORDER_NONCE = 'cb__order_nonce';

    /**
     * @var string
     */
    private $api_key;
    private $timeout = null;
    private $connect_timeout = null;

    /**
     * @var Session
     */
    private $session;

    public function __construct(string $api_key)
    {
        $this->api_key = $api_key;
        $this->session = new Session();
    }

    public function onAttributionIntercepted($func)
    {
        $interceptor = new AttributionInterceptor();
        if ($interceptor->getAttribution()->isValid()) {
            $func($interceptor->getAttribution());
        }
    }

    public function onCouponIntercepted($func)
    {
        $interceptor = new CouponInterceptor($this->api_key);
        if ($interceptor->getCoupon()->isValid()) {
            $func($interceptor->getCoupon());
        }
    }

    public function onContactIntercepted($func)
    {
        $interceptor = new ContactInterceptor($this->api_key);
        if ($interceptor->getContact()->isValid()) {
            $func($interceptor->getContact());
        }
    }

    /**
     * @param OrderBaseEvent $event
     * @return stdClass|null
     * @throws ApiException
     * @throws EventValidationException
     */
    public function sendOrderEvent(OrderBaseEvent $event): ?stdClass
    {
        // if order nonce has NOT been set by developer
        if (empty($event->getOrder()->getNonce()) && !empty($this->session->getToken())) {
            $event->getOrder()->setNonce($this->session->getToken());
        }

        $response = $this->sendEvent($event);

        $this->session->reset();

        return $response;
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

        // throw exception with error messages
        if ($validation->fails()) {
            throw new EventValidationException(print_r($validation->errors()->all(), true));
        }

        // send it to CartBoss
        $client = new ApiClient($this->api_key, $this->timeout, $this->connect_timeout);
        return $client->performHttpCall(ApiClient::HTTP_POST, 'track', $client->parseRequestBody($event->getPayload()));
    }

    /**
     * @param string $order_id
     * @return stdClass|null
     * @throws ApiException
     */
    private function getOrder(string $order_id): ?stdClass
    {
        $client = new ApiClient($this->api_key, $this->timeout, $this->connect_timeout);
        return $client->performHttpCall(ApiClient::HTTP_GET, "orders/{$order_id}");
    }

    /**
     * @param mixed $token
     * @return bool
     */
    private function isValidOrderNonce($nonce): bool
    {
        return !is_null($nonce) && 1 === preg_match("/^[a-zA-Z0-9]+$/i", $nonce);
    }
}