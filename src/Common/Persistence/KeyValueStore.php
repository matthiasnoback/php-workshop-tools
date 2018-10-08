<?php
declare(strict_types=1);

namespace Common\Persistence;

use Assert\Assertion;
use Common\String\Json;
use NaiveSerializer\JsonSerializer;
use NaiveSerializer\Serializer;

/**
 * Use this class if you need to store single values (instead of entire objects, in that case use `Common\Persistence\Database`).
 * Though the method names look like the ones Redis offers, the operations are not atomatic, like they are with Redis.
 */
final class KeyValueStore
{
    const ENV_DATABASE_DIRECTORY = 'DB_PATH';

    /**
     * Sets a value for the given key.
     *
     * @param string $key
     * @param mixed $value Anything that can be serialized using `json_encode()`
     * @return mixed The value that was set
     */
    public static function set(string $key, $value)
    {
        file_put_contents(self::determineDabaseFilePathFor($key), Json::encode($value));

        return $value;
    }

    /**
     * Returns the value that was previously set for the given key, or `null` if no value is known.
     *
     * @param string $key
     * @return mixed|null
     */
    public static function get(string $key)
    {
        $encodedValue = @file_get_contents(self::determineDabaseFilePathFor($key));
        if ($encodedValue === false) {
            return null;
        }

        return Json::decode($encodedValue);
    }

    /**
     * Increments the value of the value that was previously set for the given key. If no value is known, it will assume it to be 0.
     * It casts non-integer values to an integer if necessary.
     *
     * @param string $key
     * @return int The new value
     */
    public static function incr(string $key)
    {
        $currentValue = self::get($key);

        return self::set($key, (int)$currentValue + 1);
    }

    public static function del(string $key)
    {
        @unlink(self::determineDabaseFilePathFor($key));
    }

    /**
     * @param string $class
     * @return string The file path
     */
    private static function determineDabaseFilePathFor(string $key): string
    {
        return self::databaseDirectory() . '/' . $key . '.json';
    }

    /**
     * @return string
     */
    private static function databaseDirectory(): string
    {
        $databaseDirectory = getenv(self::ENV_DATABASE_DIRECTORY);
        Assertion::string($databaseDirectory, sprintf('Environment variable "%s" should be set', self::ENV_DATABASE_DIRECTORY));

        return $databaseDirectory;
    }
}
