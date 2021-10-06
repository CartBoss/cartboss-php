<?php

namespace CartBoss\Api;

use CartBoss\Api\Exceptions\ApiException;
use CartBoss\Api\Exceptions\ValidationException;
use CartBoss\Api\Managers\ApiClient;
use CartBoss\Api\Resources\Events\BaseEvent;
use CartBoss\Api\Resources\Events\OrderBaseEvent;
use Rakit\Validation\Validator;
use stdClass;

define('CARTBOSS_PATH', dirname(__FILE__));
define('CARTBOSS_VERSION', '1.1.0');

//require 'vendor/autoload.php';

class CartBoss
{

    /**
     * @var ApiClient
     */
    private $api_client;


    public function __construct(string $api_key)
    {
        $this->api_client = new ApiClient($api_key);
    }

    /**
     * @param OrderBaseEvent $event
     * @return stdClass|null
     * @throws ApiException
     * @throws ValidationException
     */
    public function sendOrderEvent(OrderBaseEvent $event): ?stdClass
    {
        return $this->sendEvent($event);
    }

    /**
     * @param BaseEvent $event
     * @return stdClass|null
     * @throws ValidationException
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
            throw new ValidationException(print_r($validation->errors()->all(), true));
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