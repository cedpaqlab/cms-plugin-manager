<?php
namespace Cedpaq\PluginManager\Config;

use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Database;

class MongoConfigRepository implements ConfigRepositoryInterface {
    private $db;
    private $configCollection;
    private $pluginCollection;

    // Inject MongoDB Client
    public function __construct(Client $client = null) {
        $client = $client ?? new Client("mongodb://localhost:27017");
        $this->db = $client->selectDatabase('pluginManager');

        // Try to explicitly fetch collections
        try {
            $this->configCollection = $this->db->selectCollection('config');
            $this->pluginCollection = $this->db->selectCollection('plugins');
        } catch (\Exception $e) {
            throw new \Exception("Error initializing collections: " . $e->getMessage());
        }

        if (!$this->configCollection || !$this->pluginCollection) {
            throw new \Exception("Required collections are not initialized properly.");
        }
    }


    public function get($key) {
        $document = $this->configCollection->findOne(['key' => $key]);
        return $document['value'] ?? null;
    }

    public function set($key, $value): void
    {
        $this->configCollection->updateOne(
            ['key' => $key],
            ['$set' => ['value' => $value]],
            ['upsert' => true]
        );
    }

    public function addPlugin($name, $data)
    {
        if (empty($name) || !is_array($data)) {
            throw new \InvalidArgumentException("Invalid plugin data provided.");
        }

        $updateResult = $this->pluginCollection->updateOne(
            ['name' => $name],
            ['$set' => $data],
            ['upsert' => true]
        );

        if ($updateResult->getModifiedCount() > 0 || $updateResult->getUpsertedCount() > 0) {
            return true; // Plugin successfully added or updated
        }

        return false; // No change made
    }

    public function getAllActivePlugins() {
        $cursor = $this->pluginCollection->find(['type' => 'plugin', 'enabled' => true]);
        return iterator_to_array($cursor, false); // Safely convert the cursor to an array
    }

    public function isPluginActive($pluginName) {
        $result = $this->pluginCollection->findOne(['name' => $pluginName, 'type' => 'plugin']);
        return $result !== null && $result['enabled'];
    }

    public function activatePlugin($pluginName) {
        $updateResult = $this->pluginCollection->updateOne(
            ['name' => $pluginName, 'type' => 'plugin'],
            ['$set' => ['enabled' => true]],
            ['upsert' => true]
        );

        if ($updateResult->getModifiedCount() == 0 && $updateResult->getUpsertedCount() == 0) {
            error_log("No records updated or upserted for plugin activation: $pluginName");
            throw new \Exception("Failed to activate plugin '$pluginName'.");
        }
    }


    public function deactivatePlugin($pluginName) {
        $this->pluginCollection->updateOne(
            ['name' => $pluginName, 'type' => 'plugin'],
            ['$set' => ['enabled' => false]]
        );
    }
}
