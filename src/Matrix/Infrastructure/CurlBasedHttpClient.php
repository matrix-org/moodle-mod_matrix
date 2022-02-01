<?php

declare(strict_types=1);

/**
 * @package   mod_matrix
 * @copyright 2020, New Vector Ltd (Trading as Element)
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3 or later
 */

namespace mod_matrix\Matrix\Infrastructure;

use Curl\Curl;
use mod_matrix\Matrix;

final class CurlBasedHttpClient implements HttpClient
{
    private $baseUrl;
    private $accessToken;

    public function __construct(
        Matrix\Domain\Url $baseUrl,
        Matrix\Domain\AccessToken $accessToken
    ) {
        $this->baseUrl = $baseUrl;
        $this->accessToken = $accessToken;
    }

    public function get(string $path)
    {
        $curl = $this->createCurl();

        $curl->get($path);

        self::ensureResponseDoesNotContainError($curl);

        return $curl->response;
    }

    public function post(
        string $path,
        array $body = []
    ) {
        $curl = $this->createCurl();

        $curl->post(
            $path,
            $body,
        );

        self::ensureResponseDoesNotContainError($curl);

        return $curl->response;
    }

    public function put(
        string $path,
        array $body = []
    ) {
        $curl = $this->createCurl();

        $curl->put(
            $path,
            $body,
        );

        self::ensureResponseDoesNotContainError($curl);

        return $curl->response;
    }

    private function createCurl(): Curl
    {
        $curl = new Curl();

        $curl->setDefaultJsonDecoder(true);
        $curl->setHeader('Authorization', 'Bearer ' . $this->accessToken->toString());
        $curl->setHeader('Content-Type', 'application/json');
        $curl->setUrl($this->baseUrl->toString());

        return $curl;
    }

    /**
     * @throws \RuntimeException
     */
    private static function ensureResponseDoesNotContainError(Curl $curl): void
    {
        if (!$curl->error) {
            return;
        }

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
}
