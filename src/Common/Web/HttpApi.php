<?php
declare(strict_types=1);

namespace Common\Web;

use Common\String\Json;

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
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'header' => "Accept: application/json\r\n"
            ]
        ]);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new \RuntimeException('Failed to make a request to: ' . $url);
        }

        return $response;
    }
}
