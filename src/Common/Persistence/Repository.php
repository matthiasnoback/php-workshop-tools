<?php
declare(strict_types = 1);

namespace Common\Persistence;

use Assert\Assertion;
use function Common\CommandLine\line;
use function Common\CommandLine\make_cyan;
use function Common\CommandLine\stdout;
use function DuckTyping\Object;
use NaiveSerializer\JsonSerializer;

final class Repository
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $databaseFilePath;

    public function __construct(string $className, string $databaseFilePath)
    {
        Assertion::classExists($className);
        $this->className = $className;

        Assertion::directory(dirname($databaseFilePath));
        Assertion::writeable(dirname($databaseFilePath));
        $this->databaseFilePath = $databaseFilePath;
    }

    /**
     * Provide an object that can be persisted (its `id()` method should return a string identifier. It will be serialized to disk.
     *
     * @param Entity $object
     * @return void
     */
    public function persist(Entity $object): void
    {
        $id = (string)$object->id();

        $allData = $this->loadAllObjects();
        $allData[$id] = $object;
        $this->saveAllObjects($allData);

        stdout(line(make_cyan('Persisted'), get_class($object), ':', $id));
    }

    /**
     * Provide an id and you will retrieve the unserialized version of a previously persisted object.
     *
     * @param string $id
     * @return object An object of type $this->className
     */
    public function retrieve(string $id)
    {
        $data = $this->retrieveAll();

        if (!array_key_exists($id, $data)) {
            throw new \RuntimeException(sprintf('Unable to load object of type "%s" with ID "%s"', $this->className, $id));
        }

        $object = $data[$id];

        Assertion::isInstanceOf($object, $this->className);

        return $object;
    }

    /**
     * Load all previously persisted objects managed by this repository.
     *
     * @return array
     */
    public function retrieveAll(): array
    {
        return $this->loadAllObjects();
    }

    /**
     * Delete all previously persisted objects managed by this repository.
     *
     * @return void
     */
    public function deleteAll()
    {
        if (!is_file($this->databaseFilePath)) {
            return;
        }

        unlink($this->databaseFilePath);
    }

    /**
     * Load all previously persisted objects from disk.
     *
     * @return array A list of objects of type $className
     */
    private function loadAllObjects(): array
    {
        if (!file_exists($this->databaseFilePath)) {
            return [];
        }

        $objects = JsonSerializer::deserialize("{$this->className}[]", file_get_contents($this->databaseFilePath));

        Assertion::allIsInstanceOf($objects, $this->className);

        return $objects;
    }

    /**
     * Save all objects in the given array to disk.
     *
     * @param array $allData
     * @return void
     */
    private function saveAllObjects(array $allData): void
    {
        file_put_contents($this->databaseFilePath, JsonSerializer::serialize($allData));
    }
}
