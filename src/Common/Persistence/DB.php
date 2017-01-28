<?php
declare(strict_types = 1);

namespace Common\Persistence;

use Assert\Assertion;
use function Common\CommandLine\line;
use function Common\CommandLine\make_cyan;
use function Common\CommandLine\stdout;

class DB
{
    /**
     * Provide an object that can be persisted. It will be serialized to disk.
     *
     * @param CanBePersisted $object
     */
    public static function persist(CanBePersisted $object)
    {
        $id = (string)$object->id();

        $allData = self::loadAllObjects();
        $allData[get_class($object)][$id] = $object;
        self::saveAllObjects($allData);

        stdout(line(make_cyan('Persisted'), get_class($object), ':', $id));
    }

    /**
     * Provide a class name and an id and you will retrieve the unserialized version of a previously persisted object.
     *
     * @param string $className
     * @param $id
     * @return object
     */
    public static function retrieve(string $className, string $id)
    {
        $data = static::retrieveAll($className);
        if (!array_key_exists($id, $data)) {
            throw new \RuntimeException(sprintf('Unable to load %s with ID %s', $className, $id));
        }

        return $data[$id];
    }

    /**
     * Load all previously persisted objects of a given type from disk.
     *
     * @param string $className
     * @return array
     */
    public static function retrieveAll(string $className): array
    {
        $data = self::loadAllObjects();

        return $data[$className] ?? [];
    }

    /**
     * Load all previously peristed objects from disk.
     *
     * @return array
     */
    private static function loadAllObjects() : array
    {
        if (!file_exists(self::databaseFilePath())) {
            return [];
        }

        return unserialize(file_get_contents(self::databaseFilePath()));
    }

    /**
     * Save all objects in the given array to disk.
     *
     * @param array $allData
     */
    private static function saveAllObjects(array $allData)
    {
        file_put_contents(self::databaseFilePath(), serialize($allData));
    }

    /**
     * Retrieve the path of the file in which the serialized objects get stored.
     *
     * @return string
     */
    public static function databaseFilePath()
    {
        $dbPath = getenv('DB_PATH');
        Assertion::string($dbPath, 'Env variable DB_PATH should be set');
        Assertion::directory(dirname($dbPath));
        Assertion::writeable(dirname($dbPath));

        return getenv('DB_PATH');
    }
}
