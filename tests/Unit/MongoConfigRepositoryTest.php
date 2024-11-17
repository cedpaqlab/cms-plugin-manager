<?php

use PHPUnit\Framework\TestCase;
use MongoDB\Collection;
use MongoDB\Client;
use MongoDB\Database;
use Cedpaq\PluginManager\Config\MongoConfigRepository;
use MongoDB\UpdateResult;

class MongoConfigRepositoryTest extends TestCase
{
    private $repository;
    private $mockConfigCollection;
    private $mockPluginCollection;
    private $mockUpdateResult; // Declare as a property

    public function setUp(): void
    {
        $this->mockClient = $this->createMock(Client::class);
        $this->mockDatabase = $this->createMock(Database::class);
        $this->mockConfigCollection = $this->createMock(Collection::class);
        $this->mockPluginCollection = $this->createMock(Collection::class);
        $this->mockUpdateResult = $this->createMock(UpdateResult::class); // Initialize here

        $this->mockClient->method('selectDatabase')->willReturn($this->mockDatabase);

        // Ensure selectCollection is mocked to return the right object based on input
        $this->mockDatabase->method('selectCollection')
            ->willReturnCallback(function ($collectionName) {
                if ($collectionName == 'config') {
                    return $this->mockConfigCollection;
                } elseif ($collectionName == 'plugins') {
                    return $this->mockPluginCollection;
                }
                return null;
            });

        $this->mockUpdateResult->method('getModifiedCount')->willReturn(1); // Simulate one modification

        $this->mockPluginCollection->method('updateOne')
            ->willReturn($this->mockUpdateResult); // Ensure every call to updateOne returns this mock object

        $this->repository = new MongoConfigRepository($this->mockClient);
    }

    public function testAddPlugin()
    {
        // Setup expectations for updateOne
        $this->mockPluginCollection->expects($this->once())
            ->method('updateOne')
            ->with(
                $this->equalTo(['name' => 'Logger']),
                $this->equalTo(['$set' => ['name' => 'Logger', 'type' => 'plugin', 'enabled' => true]]),
                $this->equalTo(['upsert' => true])
            )
            ->willReturn($this->mockUpdateResult); // Using the mocked UpdateResult

        // Execute the method
        $this->repository->addPlugin('Logger', ['name' => 'Logger', 'type' => 'plugin', 'enabled' => true]);
    }

    public function testGetAllActivePlugins()
    {
        $this->mockPluginCollection->method('find')
            ->with(['type' => 'plugin', 'enabled' => true])
            ->willReturn(new ArrayIterator([])); // Simulate empty result set

        $result = $this->repository->getAllActivePlugins();
        $this->assertIsArray($result);
        $this->assertCount(0, $result); // Assuming no plugins returned
    }
}
