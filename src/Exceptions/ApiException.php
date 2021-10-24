<?php

namespace CartBoss\Api\Exceptions;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use stdClass;

class ApiException extends Exception {
    /**
     * @var RequestInterface|null
     */
    protected $request;

    /**
     * @var ResponseInterface|null
     */
    protected $response;

    /**
     * ISO8601 representation of the moment this exception was thrown
     *
     * @var DateTimeImmutable
     */
    protected $raisedAt;


    public function __construct(
        $message = "",
        $code = 0,
        $request = null,
        $response = null,
        $previous = null
    ) {
        $this->raisedAt = new DateTimeImmutable();

        $formattedRaisedAt = $this->raisedAt->format(DateTimeInterface::ISO8601);
        $message = "[{$formattedRaisedAt}] " . $message;

        if (!empty($response)) {
            $this->response = $response;
        }

        $this->request = $request;
        if ($request) {
            $requestBody = $request->getBody()->__toString();

            if ($requestBody) {
                $message .= ". Request body: {$requestBody}";
            }
        }

        parent::__construct($message, $code, $previous);
    }


    /**
     * @throws ApiException
     */
    public static function createFromResponse($response, $request = null, $previous = null): ApiException {
        $object = static::parseResponseBody($response);

        $detail = print_r($object->detail, true);

        return new self(
            "Error executing API call ({$object->status}: {$object->title}): {$detail}",
            $response->getStatusCode(),
            $request,
            $response,
            $previous
        );
    }

    /**
     * @param ResponseInterface $response
     * @return stdClass
     * @throws ApiException
     */
    protected static function parseResponseBody(ResponseInterface $response): stdClass {
        $body = (string)$response->getBody();

        $object = @json_decode($body);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new self("Unable to decode CartBoss response: '{$body}'.");
        }

        return $object;
    }

    public function getResponse(): ?ResponseInterface {
        return $this->response;
    }

    /**
     * @return bool
     */
    public function hasResponse(): bool {
        return $this->response !== null;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): ?RequestInterface {
        return $this->request;
    }

    /**
     * Get the ISO8601 representation of the moment this exception was thrown
     *
     * @return DateTimeImmutable
     */
    public function getRaisedAt(): DateTimeImmutable {
        return $this->raisedAt;
    }
}