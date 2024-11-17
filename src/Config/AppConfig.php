<?php
namespace Cedpaq\PluginManager\Config;

use Cedpaq\PluginManager\Singleton\SingletonTrait;

class AppConfig {
    use SingletonTrait;

    private ConfigRepositoryInterface $configRepository;

    private function __construct(ConfigRepositoryInterface $configRepository) {
        $this->configRepository = $configRepository;
    }

    public static function getInstance(ConfigRepositoryInterface $configRepository): AppConfig
    {
        if (self::$instance === null) {
            self::$instance = new self($configRepository);
        }
        return self::$instance;
    }

    public function get($key) {
        return $this->configRepository->get($key);
    }

    public function set($key, $value): void
    {
        $this->configRepository->set($key, $value);
    }
}
