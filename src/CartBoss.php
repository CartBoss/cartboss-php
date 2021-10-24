<?php

namespace CartBoss\Api;

use CartBoss\Api\Exceptions\ApiException;
use CartBoss\Api\Exceptions\EventValidationException;
use CartBoss\Api\Interceptors\AttributionInterceptor;
use CartBoss\Api\Interceptors\ContactInterceptor;
use CartBoss\Api\Interceptors\CouponInterceptor;
use CartBoss\Api\Managers\ApiClient;
use CartBoss\Api\Resources\Events\BaseEvent;
use CartBoss\Api\Resources\Events\OrderBaseEvent;
use Rakit\Validation\Validator;
use stdClass;

define('CARTBOSS_PATH', dirname(__FILE__));
define('CARTBOSS_VERSION', '2.0.0');

class CartBoss {
    /**
     * @var string
     */
    private $api_key;
    /**
     * @var bool
     */
    private $debug;
    /**
     * @var null
     */
    private $timeout = null;
    /**
     * @var null
     */
    private $connect_timeout = null;

    public function __construct(string $api_key, bool $debug = false) {
        $this->api_key = $api_key;
        $this->debug = $debug;
    }

    public function setTimeout(int $timeout, int $connect_timeout) {
        $this->timeout = $timeout;
        $this->connect_timeout = $connect_timeout;
    }

    public function onAttributionIntercepted($func) {
        $interceptor = new AttributionInterceptor();
        if ($interceptor->getAttribution()->isValid()) {
            $func($interceptor->getAttribution());
        }
    }

    public function onCouponIntercepted($func) {
        $interceptor = new CouponInterceptor($this->api_key);
        if ($interceptor->getCoupon()->isValid()) {
            $func($interceptor->getCoupon());
        }
    }

    public function onContactIntercepted($func) {
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
    public function sendOrderEvent(OrderBaseEvent $event): ?stdClass {
        return $this->sendEvent($event);
    }

    /**
     * @param BaseEvent $event
     * @return stdClass|null
     * @throws EventValidationException
     * @throws ApiException
     */
    public function sendEvent(BaseEvent $event): ?stdClass {
        // validate event before sending
        $validator = new Validator();
        $validation = $validator->make($event->getPayload(), $event->getRules());
        $validation->validate();

        // throw exception with error messages
        if ($validation->fails()) {
            if ($this->debug) {
                error_log(print_r($validation->errors()->all(), true));
            }

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
    private function getOrder(string $order_id): ?stdClass {
        $client = new ApiClient($this->api_key, $this->timeout, $this->connect_timeout);
        return $client->performHttpCall(ApiClient::HTTP_GET, "orders/{$order_id}");
    }
}