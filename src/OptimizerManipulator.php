<?php

namespace Infinityweb\Glide\Optimizer;

use Intervention\Image\Image;
use ImageOptimizer\OptimizerFactory;
use League\Glide\Manipulators\BaseManipulator;

class OptimizerManipulator extends BaseManipulator {

    protected $dispatcher;
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
//
//        'optipng_bin' => '/usr/bin/optipng',
//        'optipng_options' => ['-i0', '-o2', '-quiet'],
//
            'pngquant_bin'      => '/usr/bin/pngquant',
            'pngquant_options'  => ['--force'],
//
//        'pngcrush_bin' => '/usr/bin/pngcrush',
//        'pngcrush_options' => ['-reduce', '-q', '-ow'],
//
//        'pngout_bin' => '/usr/bin/pngout',
//        'pngout_options' => ['-s3', '-q', '-y'],
//
            'gifsicle_bin'      => '/usr/bin/gifsicle',
            'gifsicle_options'  => ['-b', '-O5'],
            'jpegoptim_bin'     => '/usr/bin/jpegoptim',
            'jpegoptim_options' => ['--strip-all'],
//
//        'jpegtran_bin' => '/usr/bin/jpegtran',
//        'jpegtran_options' => ['-optimize', '-progressive'],
//
//        'advpng_bin' => '/usr/bin/advpng',
//        'advpng_options' => ['-z', '-4', '-q'],
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
    public function __construct(array $config = null) {

        if ($config) {
            $this->config = $config + $this->options;
        }

        $this->optimizerFactory = new OptimizerFactory();
    }

    /**
     * Perform optimize image.
     * @param  Image $image The source image.
     * @return Image The manipulated image.
     */
    public function run(Image $image) {

        $tmp = tempnam(sys_get_temp_dir(), 'GlideMinify');

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
