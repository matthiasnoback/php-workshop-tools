<?php
declare(strict_types = 1);

namespace Common\Persistence;

use Assert\Assertion;
use function Common\CommandLine\line;
use function Common\CommandLine\make_cyan;
use function Common\CommandLine\stdout;
use Common\Serialization\JsonSerializer;

class DB
{
    /**
     * Provide an object that can be persisted. It will be serialized to disk.
     *
     * @param CanBePersisted $object
     */
    public static function persist(CanBePersisted $object)
    {
        $serializedObject = JsonSerializer::serialize($object);

        $filename = self::determineFilenameFor(get_class($object), (string)$object->id());
        @mkdir(dirname($filename), 0777, true);
        file_put_contents($filename, $serializedObject);

        stdout(line(make_cyan('Persisted'), get_class($object), ':', (string)$object->id()));
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
        $filename = self::determineFilenameFor($className, $id);

        if (!is_file($filename)) {
            throw new \RuntimeException(sprintf('Unable to load %s with ID %s', $className, $id));
        }

        return JsonSerializer::deserialize($className, file_get_contents($filename));
    }

    /**
     * Load all previously persisted objects of a given type from disk.
     *
     * @param string $className
     * @return array
     */
    public static function retrieveAll(string $className): array
    {
        $files = self::allFilesFor($className);
        return array_map(function (string $filename) use ($className) {
            return JsonSerializer::deserialize($className, file_get_contents($filename));
        }, $files);

        return $data[$className] ?? [];
    }

    public static function deleteAll(string $className)
    {
        $files = self::allFilesFor($className);

        foreach ($files as $file) {
            unlink($file);
        }
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

    private static function determineFilenameFor(string $class, string $id)
    {
        $filename = self::determineDirectoryFor($class) . '/' . $id . '.json';

        return $filename;
    }

    private static function determineDirectoryFor(string $class)
    {
        return self::databaseFilePath() . '/' . str_replace('\\', '_', $class);
    }

    /**
     * @param string $className
     * @return array
     */
    private static function allFilesFor(string $className)
    {
        $directory = self::determineDirectoryFor($className);

        $files = glob($directory . '/*.json');
        return $files;
    }
}
