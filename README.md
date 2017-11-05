# Glide Optimizer Manipulator

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/infinityweb/glide-optimizer.svg?style=flat-square)](https://packagist.org/packages/infinityweb/glide-optimizer)

This manipulator uses ps/image-optimizer package to minify resources.

Inspired by https://github.com/approached/laravel-image-optimizer

## Installation

- Recommend convert packages:
```bash
sudo apt-get install pngquant gifsicle jpegoptim
```

- Require this package with composer:
```bash
composer require infinityweb/glide-optimizer
```

## Usage

```php
$server = League\Glide\ServerFactory::create([
    'source' => 'path/to/source/folder',
    'cache' => 'path/to/cache/folder',
]);

$manipulators = $server->getApi()->getManipulators();
$manipulators[] = new Infinityweb\Glide\Optimizer\OptimizerManipulator();

$server->getApi()->setManipulators($manipulators);
```
