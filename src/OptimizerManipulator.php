<?php

namespace Infinityweb\Glide\Optimizer;

use Intervention\Image\Image;
use ImageOptimizer\OptimizerFactory;
use League\Glide\Manipulators\BaseManipulator;
use Psr\Log\LoggerInterface;

class OptimizerManipulator extends BaseManipulator {

    protected $optimizerFactory = null;
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
        'options'           => [
        ],
        /*
          |--------------------------------------------------------------------------
          | Transformer for image
          |--------------------------------------------------------------------------
          |
          | You can choice which tranformer you will use
          |
         */
        'transform_handler' => [
            'image/x-png' => 'pngquant',
            'image/png'   => 'pngquant',
            'image/pjpeg' => 'jpegoptim',
            'image/jpeg'  => 'jpegoptim',
            'image/gif'   => 'gifsicle',
        ],
    ];

    /**
     * Create Optimizer instance.
     * @param array $config 
     */
    public function __construct(array $config = null, LoggerInterface $logger = null) {

        if ($config) {
            $this->config = $config + $this->options;
        }

        $this->optimizerFactory = new OptimizerFactory($this->config['options'], $logger);
    }

    /**
     * Perform optimize image.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image) {

        $tmp = tempnam(sys_get_temp_dir(), 'GlideOptimizer');

        file_put_contents($tmp, $image->getEncoded());

        $imageTmp = \Intervention\Image\ImageManagerStatic::make($tmp);

        if (!isset($this->config['transform_handler'][$imageTmp->mime()])) {
            throw new \Exception('TransformHandler for file mime: "' . $image->mime() . '" was not found');
        }

        $this->optimizerFactory->get($this->config['transform_handler'][$imageTmp->mime()])->optimize($tmp);

        $image->setEncoded(file_get_contents($tmp));

        unlink($tmp);

        return $image;
    }

}
