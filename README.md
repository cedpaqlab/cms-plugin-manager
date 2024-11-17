# CMS Plugin Manager

**CMS Plugin Manager** is an advanced PHP library for managing plugins in a CMS. It leverages advanced PHP concepts and design patterns to provide a flexible, extensible, and efficient solution.

## Overview
This PHP application manages a dynamic plugin system, integrating advanced software design patterns and MongoDB for effective data management and operations. The system allows for the conditional loading and activation of plugins based on predefined configurations and dependency relationships stored in a MongoDB database.

## Features

- Dynamically enable and disable plugins.
- Effortlessly manage plugin configurations.
- Extend application functionality without modifying the core code.
- Utilizes **Singleton**, **Factory**, **Repository**, and **Dependency Injection** patterns for efficient, modular code.
- Fully compatible with PHP 8.3.

## Installation

Install the library using [Composer](https://getcomposer.org/):

```bash
composer require cedpaqlab/cms-plugin-manager
```
## Setup
- Database Setup: Install MongoDB and ensure it is running.

## Usage

### Initialization

Here's a basic example of how to integrate the library into your CMS:

```php
require_once 'vendor/autoload.php';

$configRepository = new MongoConfigRepository();
$pluginManager = new PluginManager($configRepository);

$pluginConfigs = [
    'Logger' => ['enabled' => true],
    'Cache' => ['enabled' => true],
    'SEO' => ['enabled' => true, 'dependencies' => ['Logger']],
    'CMS' => ['enabled' => true, 'dependencies' => ['SEO', 'Cache']]
];

$pluginManager->initializePlugins($pluginConfigs);
echo "Loaded plugins: " . $pluginManager->listLoadedPlugins();

```

## Testing

Run tests using [PHPUnit](https://phpunit.de/):

```bash
vendor/bin/phpunit
```

## Contribution

Contributions are welcome! Please open an [issue](https://github.com/cedpaqlab/cms-plugin-manager/issues) or submit a pull request.

## License

This project is licensed under the [MIT License](LICENSE).
