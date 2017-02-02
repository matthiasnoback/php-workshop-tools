<?php
declare(strict_types = 1);

namespace Common\Serialization;

use Assert\Assertion;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Float_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;

final class JsonSerializer
{
    private $contextFactory;
    private $docblockFactory;

    public static function deserialize(string $class, string $jsonEncodedData)
    {
        return (new self())->doDeserialize($class, $jsonEncodedData);
    }

    public static function serialize($object)
    {
        return (new self())->doSerialize($object);
    }

    private function __construct()
    {
        $this->contextFactory = new ContextFactory();
        $this->docblockFactory = DocBlockFactory::createInstance();
    }

    private function doDeserialize(string $class, string $jsonEncodedData)
    {
        return self::hydrate(new Object_(new Fqsen('\\' . $class)), json_decode($jsonEncodedData, true));
    }

    private function hydrate(Type $type, $data)
    {
        if ($data === null) {
            return null;
        }
        if ($type instanceof String_) {
            return (string)$data;
        }
        if ($type instanceof Integer) {
            return (integer)$data;
        }
        if ($type instanceof Boolean) {
            return (boolean)$data;
        }
        if ($type instanceof Float_) {
            return (float)$data;
        }

        if ($type instanceof Object_) {
            $reflection = new \ReflectionClass((string)$type);
            $object = $reflection->newInstanceWithoutConstructor();
            foreach ($reflection->getProperties() as $property) {
                if (!isset($data[$property->getName()])) {
                    continue;
                }

                $propertyType = $this->resolvePropertyType($property, $reflection);
                $property->setAccessible(true);
                $property->setValue($object, self::hydrate($propertyType, $data[$property->getName()]));
            }
            return $object;
        }

        if ($type instanceof Array_) {
            Assertion::isArray($data);
            Assertion::isInstanceOf($type->getValueType(), Object_::class, 'Only lists of objects are supported');

            $processed = [];
            foreach ($data as $elementData) {
                $processed[] = self::hydrate($type->getValueType(), $elementData);
            }

            return $processed;
        }

        throw new \LogicException('Unsupported type: ' . get_class($type));
    }

    private function doSerialize($object)
    {
        return json_encode($this->extract($object), JSON_PRETTY_PRINT);
    }

    private function extract($object)
    {
        if (is_object($object)) {
            $data = [];

            $reflection = new \ReflectionClass(get_class($object));
            foreach ($reflection->getProperties() as $property) {
                $property->setAccessible(true);
                $data[$property->getName()] = $property->getValue($object);
            }

            return $data;
        }

        if (is_array($object)) {
            $data = [];
            foreach ($object as $element) {
                $data[] = $this->extract($element);
            }

            return $data;
        }

        if (!is_scalar($object)) {
            throw new \LogicException(sprintf(
                'You can only serialize objects, arrays and scalar values, got: %s',
                var_export($object, true)
            ));
        }

        return $object;
    }

    private function resolvePropertyType(\ReflectionProperty $property, \ReflectionClass $class) : Type
    {
        $fileName = $class->getFileName();
        Assertion::file($fileName, sprintf(
            'Class "%s" has no source file, maybe it is a PHP built-in class?',
            $class->getName()
        ));
        $context = $this->contextFactory->createForNamespace(
            $class->getNamespaceName(),
            file_get_contents($fileName)
        );

        $docblock = $this->docblockFactory->create($property->getDocComment(), $context);
        $varTags = $docblock->getTagsByName('var');
        Assertion::count(
            $varTags,
            1,
            sprintf('You need to add an @var annotation to property "%s"', $property->getName())
        );
        /** @var Var_[] $varTags */
        $propertyType = $varTags[0]->getType();

        return $propertyType;
    }
}
