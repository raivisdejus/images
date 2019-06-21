<?php
/**
 * ScandiPWA_Images
 *
 * @category    ScandiPWA
 * @package     ScandiPWA_Images
 * @author      Raivis Dejus <info@scandiweb.com>
 * @copyright   Copyright (c) 2019 Scandiweb, Ltd (https://scandiweb.com)
 */

declare(strict_types=1);

namespace ScandiPWA\Images\Plugin\Image;

use \Magento\Framework\Image;
use \WebPConvert\WebPConvert;
use \Gumlet\ImageResize;

/**
 * Class CreateWebp
 *
 * @package ScandiPWA\Images\Plugin\Image
 */
class CreateWebp
{
    /**
     * Will add webp images after original images are saved
     *
     * @param Image $subject
     * @param string $destination                                                                                             $result
     * @param string $newFileName                                                                                             $result
     *
     * @return void
     */
    public function afterSave(
        Image $subject,
        $destination,
        $newFileName
    ) {
        // Convert to webp
        $destination = $this->replaceExtension($newFileName, '.webp');
        WebPConvert::convert($newFileName, $destination, []);

        // Create webp image for primitive
        $primitiveTmp = $this->replaceExtension($newFileName, '.jpg', '.primitive');
        $primitiveFileName = $this->replaceExtension($newFileName, '.webp', '.primitive');
        $primitive = new ImageResize($newFileName);
        $primitive->resizeToWidth(30);
        $primitive->save($primitiveTmp);
        WebPConvert::convert($primitiveTmp, $primitiveFileName, []);
        unlink($primitiveTmp);
    }

    /**
     * @param string $filename
     * @param string $newExtension
     * @param string $filenamePostfix
     * @return string
     */
    public function replaceExtension($filename, $newExtension, $filenamePostfix = '') {
        $info = pathinfo($filename);
        return $info['dirname'] . '/' . $info['filename'] . $filenamePostfix . $newExtension;
    }
}
