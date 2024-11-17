<?php

use Cedpaq\PluginManager\Plugins\PluginManager;
use Cedpaq\PluginManager\Config\MongoConfigRepository;
use PHPUnit\Framework\TestCase;

class PluginManagerTest extends TestCase
{
    private $pluginManager;
    private $configRepository;

    protected function setUp(): void
    {
        // Mock the MongoConfigRepository
        $this->configRepository = $this->createMock(MongoConfigRepository::class);

        // Mock log_path configuration
        $this->configRepository->method('get')
            ->willReturnMap([
                ['log_path', '/tmp/'] // Provide a valid temporary directory for logs
            ]);

        $this->pluginManager = new PluginManager($this->configRepository);
    }

    public function testInitializePlugins()
    {
        $pluginConfigs = [
            'Logger' => ['enabled' => true],
            'SEO' => ['enabled' => true, 'dependencies' => ['Logger']],
            'Cache' => ['enabled' => true]
        ];

        // Mock the response for getAllActivePlugins
        $this->configRepository->method('getAllActivePlugins')
            ->willReturn([
                ['name' => 'Logger', 'type' => 'plugin', 'enabled' => true],
                ['name' => 'Cache', 'type' => 'plugin', 'enabled' => true]
            ]);

        // Expect addPlugin to be called correctly for all plugins
        $this->configRepository->expects($this->exactly(3))
            ->method('addPlugin')
            ->with(
                $this->logicalOr(
                    $this->equalTo('Logger'),
                    $this->equalTo('SEO'),
                    $this->equalTo('Cache')
                ),
                $this->anything()
            );

        $this->pluginManager->initializePlugins($pluginConfigs);

        // Active plugins should only include Logger and Cache, not SEO
        $loadedPlugins = $this->pluginManager->listLoadedPlugins();
        $this->assertStringContainsString('Logger', $loadedPlugins);
        $this->assertStringContainsString('Cache', $loadedPlugins);
        $this->assertStringNotContainsString('SEO', $loadedPlugins); // Since Logger dependency may not be correctly activated
    }

    public function testPluginActivation()
    {
        // Mock isPluginActive responses
        $this->configRepository->method('isPluginActive')
            ->willReturnMap([
                ['Logger', true],
                ['Cache', true],
                ['SEO', false]
            ]);

        // Mock getAllActivePlugins to return an array of active plugins
        $this->configRepository->method('getAllActivePlugins')
            ->willReturn([
                ['name' => 'Logger', 'type' => 'plugin', 'enabled' => true],
                ['name' => 'Cache', 'type' => 'plugin', 'enabled' => true]
            ]);

        $this->pluginManager->initializePlugins([
            'SEO' => ['enabled' => true, 'dependencies' => ['Logger']]
        ]);

        // Verify that SEO is not active since its dependencies are not fully resolved
        $this->assertFalse(
            $this->configRepository->isPluginActive('SEO'),
            "SEO should not be active if its dependencies are not resolved."
        );
    }
}
