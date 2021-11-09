<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Infrastructure;

use Curl\Curl;

final class CurlBasedHttpClient implements HttpClient
{
    private $baseUrl;
    private $accessToken;

    public function __construct(
        string $baseUrl,
        string $accessToken
    ) {
        $this->baseUrl = $baseUrl;
        $this->accessToken = $accessToken;
    }

    public function request(
        string $method,
        string $path,
        array $qs = [],
        array $body = []
    ) {
        $allowedMethods = [
            'GET',
            'POST',
            'PUT',
        ];

        if (!\in_array($method, $allowedMethods, true)) {
            throw new \InvalidArgumentException('unknown method: ' . $method);
        }

        $curl = new Curl();

        $curl->setDefaultJsonDecoder(true);
        $curl->setHeader('Authorization', 'Bearer ' . $this->accessToken);
        $curl->setHeader('Content-Type', 'application/json');

        if ('GET' === $method) {
            $curl->get($this->baseUrl . $path, $qs);
        } elseif ('POST' === $method) {
            $curl->setUrl($this->baseUrl . $path, $qs);
            $curl->post($curl->getUrl(), $body);
        } elseif ('PUT' === $method) {
            $curl->setUrl($this->baseUrl . $path, $qs);
            $curl->put($curl->getUrl(), $body);
        }

        if ($curl->error) {
            $httpStatusCode = $curl->httpStatusCode;
            $httpErrorMessage = $curl->httpErrorMessage;

            if (
                \is_array($curl->response)
                && \array_key_exists('errcode', $curl->response)
                && \array_key_exists('error', $curl->response)
            ) {
                $errorCode = $curl->response['errcode'];
                $errorMessage = $curl->response['error'];

                throw new \RuntimeException(
                    <<<TXT
Sending a request failed with HTTP status code {$httpStatusCode} and error message {$httpErrorMessage}.

The response contains a specific error code and message.

Error code
---------

{$errorCode}

Error message
---------

{$errorMessage}

TXT
                );
            }

            throw new \RuntimeException(
                <<<TXT
Sending a request failed with HTTP status code {$httpStatusCode} and error message {$httpErrorMessage}.
TXT
            );
        }

        return $curl->response;
    }
}
