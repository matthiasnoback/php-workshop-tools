<?php
declare(strict_types=1);

namespace Common\Web;

use Buzz\Browser;
use Buzz\Client\FileGetContents;
use Common\String\Json;
use Psr\Http\Message\ResponseInterface;

final class HttpApi
{
    /**
     * Fetch a response and JSON-decode it in one go.
     *
     * @param string $url
     * @return mixed
     */
    public static function fetchDecodedJsonResponse(string $url)
    {
        return Json::decode(self::fetchJsonResponse($url));
    }

    /**
     * Fetch the HTTP response for a given URL (makes a GET request).
     *
     * @param string $url
     * @return string
     */
    public static function fetchJsonResponse(string $url): string
    {
        $response = self::getBrowser()->get($url, [
            'Accept' => 'application/json',
            'X-Internal-Request' => 'true'
        ]);

        self::handleFailure($response);

        return $response->getBody()->getContents();
    }

    /**
     * @param string $url
     * @param array $data An array of data which will be encoded as form data
     * @return string The response content
     * @throws \RuntimeException
     */
    public static function postFormData(string $url, array $data): string
    {
        $response = self::getBrowser()->submitForm($url, $data, 'POST', [
            'Content-type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
            'X-Internal-Request' => 'true'
        ]);

        self::handleFailure($response);

        return $response->getBody()->getContents();
    }

    private static function getBrowser(): Browser
    {
        return new Browser(new FileGetContents(['timeout' => 5]));
    }

    private static function handleFailure(ResponseInterface $response): void
    {
        if ($response->getStatusCode() >= 400) {
            throw new \RuntimeException(sprintf(
                'Failed HTTP response. Status: %d %s. Body: %s',
                $response->getStatusCode(),
                $response->getReasonPhrase(),
                $response->getBody()->getContents()
            ));
        }
    }
}
