<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace app\components;

use Imagine\Image\ImageInterface;
use yii\web\HttpException;

/**
 * Помощник Image.
 * Содержит все необходимые функции для работы с изображениями, доболняя Imagine.
 */
class ImageHelper
{
    /**
     * Уменьшает или увеличивает изображение.
     *
     * @param       $source
     * @param       $width
     * @param       $height
     * @param array $params
     *
     * @return string
     */
    public static function resize($source, $width, $height, $params = [])
    {
        $uploadsPath = \Yii::$app->basePath . '/web';
        $sourceName = $uploadsPath . $source;
        if (!is_file($sourceName)) {
            return '';
        }

        $baseName = basename($source);
        $_partsExt = explode('.', $baseName);
        $ext = '.' . strtolower(end($_partsExt));
        $fileName = basename($baseName, $ext) . '_' . $width . 'x' . $height . $ext;
        $destination = '/uploads/resized/' . $fileName;
        $destinationPath = $uploadsPath . $destination;

        //  Если такой файл есть, то ничего ресайзить не надо.
        if (is_file($destinationPath)) {
            return $destination;
        }

        $imagine = new \Imagine\Imagick\Imagine();
        $image = $imagine->open($sourceName);
        //$geometry = $image->getSize();

        if ($width < 0) {
            $width = 0;
        }
        if ($height < 0) {
            $height = 0;
        }

        try {
            $image->getImagick()->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, true);
            $image->save($destinationPath);
        } catch (\RuntimeException $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        }

        return $destination;
    }

    /**
     * Обрезает изображение.
     *
     * @param string  $source
     * @param integer $width
     * @param integer $height
     * @param array   $params
     *
     * @return string
     */
    public static function thumbnail($source, $width, $height, $params = [])
    {
        $uploadsPath = \Yii::$app->basePath . '/web';
        $sourceName = $uploadsPath . $source;
        if (!is_file($sourceName)) {
            return '';
        }

        $baseName = basename($source);
        $_partsExt = explode('.', $baseName);
        $ext = '.' . strtolower(end($_partsExt));
        $fileName = basename($baseName, $ext) . '_' . $width . 'x' . $height . 't' . $ext;
        $destination = '/uploads/resized/' . $fileName;
        $destinationPath = $uploadsPath . $destination;

        //  Если такой файл есть, то ничего ресайзить не надо.
        if (is_file($destinationPath)) {
            return $destination;
        }

        $imagine = new \Imagine\Imagick\Imagine();
        $image = $imagine->open($sourceName);
        $geometry = $image->getSize();

        if ($width < 0) {
            $width = 0;
        }
        if ($height < 0) {
            $height = 0;
        }

        try {
            $image
                ->thumbnail(new \Imagine\Image\Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND)
                ->save($destinationPath);
        } catch (\RuntimeException $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        }

        return $destination;
    }

    /**
     * Вернет html img тег, с сылкой на изображение с новыми размерами.
     *
     * @param string  $source
     * @param integer $width
     * @param integer $height
     * @param array   $params
     *
     * @return string
     */
    public static function img($source, $width, $height, $params = [])
    {
        $alt = isset($params['alt']) ? $params['alt'] : '';
        return isset($params['thumbnail']) && $params['thumbnail']
            ? '<img src="' . static::thumbnail($source, $width, $height, $params) . '" alt="' . $alt . '" />'
            : '<img src="' . static::resize($source, $width, $height, $params) . '" alt="' . $alt . '" />';
    }

    /**
     * Вернет сылку на изображение с новыми размерами.
     *
     * @param string  $source
     * @param integer $width
     * @param integer $height
     * @param array   $params
     *
     * @return string
     */
    public static function url($source, $width, $height, $params = [])
    {
        return isset($params['thumbnail']) && $params['thumbnail']
            ? static::thumbnail($source, $width, $height, $params)
            : static::resize($source, $width, $height, $params);
    }
}