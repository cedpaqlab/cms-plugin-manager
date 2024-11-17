<?php
namespace Cedpaq\PluginManager\Config;

use MongoDB\Client;

class MongoConfigRepository implements ConfigRepositoryInterface {
    private $db;
    private $configCollection;
    private $pluginCollection;

    public function __construct() {
        $client = new Client("mongodb://localhost:27017");
        $this->db = $client->selectDatabase('pluginManager');
        $this->configCollection = $this->db->config;
        $this->pluginCollection = $this->db->plugins;
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

    public function addPlugin($pluginName, $config) {
        $this->pluginCollection->updateOne(
            ['name' => $pluginName, 'type' => 'plugin'],
            ['$set' => $config],
            ['upsert' => true]
        );
    }

    public function getAllActivePlugins() {
        return $this->pluginCollection->find(['type' => 'plugin', 'enabled' => true]);
    }

    public function isPluginActive($pluginName) {
        $result = $this->pluginCollection->findOne(['name' => $pluginName, 'type' => 'plugin']);
        return $result !== null && $result['enabled'];
    }

    public function activatePlugin($pluginName) {
        $this->pluginCollection->updateOne(
            ['name' => $pluginName, 'type' => 'plugin'],
            ['$set' => ['enabled' => true]],
            ['upsert' => true]
        );
    }

    public function deactivatePlugin($pluginName) {
        $this->pluginCollection->updateOne(
            ['name' => $pluginName, 'type' => 'plugin'],
            ['$set' => ['enabled' => false]]
        );
    }
}
