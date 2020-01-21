<?php

namespace Infinityweb\Glide\Optimizer;

use ImageOptimizer\OptimizerFactory;
use Intervention\Image\Image;
use League\Glide\Manipulators\BaseManipulator;
use Psr\Log\LoggerInterface;

/**
 * Class OptimizerManipulator
 * @package Infinityweb\Glide\Optimizer
 */
class OptimizerManipulator extends BaseManipulator
{
    /**
     * @var OptimizerFactory|null
     */
    protected $optimizerFactory = null;

    /**
     * @var array
     */
    protected $config = [
        /*
          |--------------------------------------------------------------------------
          | Options for image transforming
          |--------------------------------------------------------------------------
          |
          | Bin path you can check easy with follow command in a shell:
          | which optipng
          |
         */
        'options' => [],
        /*
          |--------------------------------------------------------------------------
          | Transformer for image
          |--------------------------------------------------------------------------
          |
          | You can choice which transformer you will use
          |
         */
        'transform_handler' => [
            'image/x-png' => 'pngquant',
            'image/png' => 'pngquant',
            'image/pjpeg' => 'jpegoptim',
            'image/jpeg' => 'jpegoptim',
            'image/gif' => 'gifsicle',
        ],
    ];

    /**
     * Create Optimizer instance.
     *
     * @param array $config
     * @param LoggerInterface|null $logger
     */
    public function __construct(array $config = [], LoggerInterface $logger = null)
    {
        $this->config['options'] = array_merge($this->config['options'], $config);

        $this->optimizerFactory = new OptimizerFactory($this->config['options'], $logger);
    }

    /**
     * Perform optimize image.
     *
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     * @return Image
     * @throws \Exception
     */
    public function run(Image $image)
    {
        $tmp = tempnam(sys_get_temp_dir(), 'GlideOptimizer');

        file_put_contents($tmp, $image->getEncoded());

        $imageTmp = \Intervention\Image\ImageManagerStatic::make($tmp);
        $imageTmp->setDriver($image->getDriver());

        if (!isset($this->config['transform_handler'][$imageTmp->mime()])) {
            throw new \Exception('TransformHandler for file mime: "' . $image->mime() . '" was not found');
        }

        $this->optimizerFactory->get($this->config['transform_handler'][$imageTmp->mime()])->optimize($tmp);

        $image->setEncoded(file_get_contents($tmp));

        unlink($tmp);

        return $image;
    }
}
