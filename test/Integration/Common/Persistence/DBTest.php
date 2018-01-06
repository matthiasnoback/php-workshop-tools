<?php
declare(strict_types = 1);

namespace Test\Integration\Common\Persistence;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Common\Persistence\Database;

class DBTest extends TestCase
{
    protected function setUp()
    {
        Database::deleteAll(PersistableDummy::class);
    }

    /**
     * @test
     */
    public function initially_it_will_return_an_empty_list_of_objects()
    {
        $this->assertEquals([], Database::retrieveAll(PersistableDummy::class));
    }

    /**
     * @test
     */
    public function it_persists_and_retrieves_objects_by_their_id()
    {
        $id = Uuid::uuid4();
        $object = new PersistableDummy(new DummyId((string)$id));

        Database::persist($object);

        $retrievedObject = Database::retrieve(get_class($object), (string)$id);

        $this->assertEquals($object, $retrievedObject);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_for_non_persisted_objects()
    {
        $this->expectException(\RuntimeException::class);

        Database::retrieve(PersistableDummy::class, (string)Uuid::uuid4());
    }

    /**
     * @test
     */
    public function it_retrieves_all_objects_by_classname()
    {
        Database::persist(new PersistableDummy(new DummyId((string)Uuid::uuid4())));
        Database::persist(new PersistableDummy(new DummyId((string)Uuid::uuid4())));

        $this->assertCount(2, Database::retrieveAll(PersistableDummy::class));
    }
}
