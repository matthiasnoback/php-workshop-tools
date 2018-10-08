<?php
declare(strict_types=1);

namespace Common\String;

final class Json
{
    /**
     * @param mixed $data
     * @return string
     */
    public static function encode($data): string
    {
        $result = json_encode($data);

        if ($result === false && json_last_error()) {
            throw new \InvalidArgumentException('JSON encoding error: ' . json_last_error_msg());
        }

        return $result;
    }

    /**
     * @param string $string
     * @param bool $convertObjectIntoAssociativeArray
     * @return mixed
     */
    public static function decode(string $string, $convertObjectIntoAssociativeArray = false)
    {
        $result = json_decode($string, $convertObjectIntoAssociativeArray);

        if ($result === null && json_last_error()) {
            throw new \InvalidArgumentException('JSON decoding error: ' . json_last_error_msg());
        }

        return $result;
    }
}
