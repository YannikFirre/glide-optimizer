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

    use League\Glide\Responses\SymfonyResponseFactory;
    use League\Flysystem\Filesystem;

    $cache = new Filesystem('cache/');
    $source = new Filesystem('source/');

    $imageManager = new Intervention\Image\ImageManager([
        'driver' => 'imagick',
    ]);

    $manipulators = [
        new League\Glide\Manipulators\Size(2000 * 2000),
        new League\Glide\Manipulators\Encode(),
        new Infinityweb\Glide\Optimizer\OptimizerManipulator(),
    ];

    // Setup Glide server
    $server = new League\Glide\Server(
            $source, $cache, new League\Glide\Api\Api($imageManager, $manipulators)
    );

```