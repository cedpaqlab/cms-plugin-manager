<?php

use PHPUnit\Framework\TestCase;
use Cedpaq\PluginManager\Config\MongoConfigRepository;
use Cedpaq\PluginManager\Plugins\PluginManager;
use MongoDB\Client;

class PluginFeatureTest extends TestCase
{
    private $pluginManager;
    private $repository;

    protected function setUp(): void
    {
        $client = new Client("mongodb://localhost:27017");
        $this->repository = new MongoConfigRepository($client);
        $this->pluginManager = new PluginManager($this->repository);

        // Clear the database before each test
        $client->selectDatabase('pluginManagerTest')->drop();

        // Set a temporary log path
        $this->repository->set('log_path', sys_get_temp_dir() . '/');
    }

    public function testPluginInitializationAndActivation()
    {
        $pluginConfigs = [
            'Logger' => ['enabled' => true],
            'SEO' => ['enabled' => true, 'dependencies' => ['Logger']],
            'Cache' => ['enabled' => true],
            'CMS' => ['enabled' => true, 'dependencies' => ['SEO', 'Cache']],
        ];

        // Initialize and activate plugins
        $this->pluginManager->initializePlugins($pluginConfigs);

        // Check if all plugins are loaded
        $this->assertTrue($this->repository->isPluginActive('Logger'));
        $this->assertTrue($this->repository->isPluginActive('SEO'));
        $this->assertTrue($this->repository->isPluginActive('Cache'));
        $this->assertTrue($this->repository->isPluginActive('CMS'));
    }

    public function testDeactivatePlugin()
    {
        $pluginConfigs = ['Logger' => ['enabled' => true]];
        $this->pluginManager->initializePlugins($pluginConfigs);

        // Assert Logger is active before deactivation
        $this->assertTrue($this->repository->isPluginActive('Logger'), "Logger should be active before deactivation.");

        // Deactivate Logger
        $this->pluginManager->deactivatePlugin('Logger');

        // Verify Logger is deactivated
        $this->assertFalse($this->repository->isPluginActive('Logger'), "Logger should be deactivated.");
    }

}
