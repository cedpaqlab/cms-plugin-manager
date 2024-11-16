# CMS Plugin Manager

**CMS Plugin Manager** is an advanced PHP library for managing plugins in a CMS. It leverages advanced PHP concepts and design patterns to provide a flexible, extensible, and efficient solution.

## Features

- Dynamically enable and disable plugins.
- Effortlessly manage plugin configurations.
- Extend application functionality without modifying the core code.
- Implements design patterns such as **Factory**, **Observer**, and **Dependency Injection**.
- Fully compatible with PHP 8.3.

## Installation

Install the library using [Composer](https://getcomposer.org/):

```bash
composer require cedpaqlab/cms-plugin-manager
```

## Usage

### Initialization

Here's a basic example of how to integrate the library into your CMS:

```php
require 'vendor/autoload.php';

use MonProjet\PluginManager;

// Initialize the plugin manager
$pluginManager = new PluginManager();

// Load plugins
$pluginManager->loadPlugins('path/to/plugins');

// Activate a plugin
$pluginManager->activate('plugin-name');

// Deactivate a plugin
$pluginManager->deactivate('plugin-name');

// Get the list of active plugins
$activePlugins = $pluginManager->getActivePlugins();
```

### Plugin Structure

Each plugin should follow a standard structure, including a main class and a configuration file. For example:

```
my-plugin/
├── Plugin.php
├── config.php
└── assets/
```

#### Example `Plugin.php`:

```php
namespace MyPlugin;

class Plugin {
    public function activate() {
        // Code to execute on activation
    }

    public function deactivate() {
        // Code to execute on deactivation
    }
}
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
