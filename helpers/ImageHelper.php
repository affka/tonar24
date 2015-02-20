<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace helpers;

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
        $uploadsPath = \Yii::$app->basePath . '/web/uploads/original/';
        $sourceName = $uploadsPath . $source;
        if (!is_file($sourceName)) {
            return '';
        }

        $_parts = explode(':', basename($source));
        $_partsExt = explode('.', $_parts[1]);
        $ext = '.' . end($_partsExt);
        $fileName = basename($_parts[1], $ext) . '_' . $width . 'x' . $height . $ext;
        $destination = 'uploads/resized/' . $fileName;
        $destinationPath = $uploadsPath . $destination;

        //  Если такой файл есть, то ничего ресайзить не надо.
        if (is_file($destinationPath)) {
            return '/' . $destination;
        }

        $imagine = new \Imagine\Imagick\Imagine();
        $image = $imagine->open($sourceName);
        $geometry = $image->getSize();

        if ($width > $geometry->getWidth()) {
            $width = $geometry->getWidth();
        }
        if ($width < 0) {
            $width = 0;
        }
        if ($height > $geometry->getHeight()) {
            $height = $geometry->getHeight();
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

        return '/' . $destination;
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
    public static function crop($source, $width, $height, $params = [])
    {
        /*$sourceName = public_path() . '/files/images/' . str_replace(':', '/originals/', $source);
        if (!is_file($sourceName)) {
            return '';
        }

        $_parts = explode(':', basename($source));
        $_partsExt = explode('.', $_parts[1]);
        $ext = '.' . end($_partsExt);
        $fileName = basename($_parts[1], $ext) . '_' . $width . 'x' . $height . 'c' . $ext;
        $destination = dirname(str_replace(':', '/resized/', $source)) . '/' . $fileName;
        $destinationPath = public_path() . '/files/images/' . $destination;

        //  Если такой файл есть, то ничего ресайзить не надо.
        if (is_file($destinationPath)) {
            return '/files/images/' . $destination;
        }

        $imagine = new \Imagine\Imagick\Imagine();
        $image = $imagine->open($sourceName);
        $geometry = $image->getSize();

        if ($width > $geometry->getWidth()) {
            $width = $geometry->getWidth();
        }
        if ($width < 0) {
            $width = 0;
        }
        if ($height > $geometry->getHeight()) {
            $height = $geometry->getHeight();
        }
        if ($height < 0) {
            $height = 0;
        }

        $offset = new \Imagine\Image\Point(
            ($geometry->getWidth() - $width) / 2,
            ($geometry->getHeight() - $height) / 2
        );

        try {
            $image->crop($offset, new \Imagine\Image\Box($width, $height))
                ->save($destinationPath);
        } catch (\RuntimeException $e) {
            \App::abort($e->getCode(), $e->getMessage());
        }

        return '/files/images/' . $destination;*/
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
        return isset($params['crop']) && $params['crop']
            ? '<img src="' . static::crop($source, $width, $height, $params) . '" alt="' . $alt . '" />'
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
        return isset($params['crop']) && $params['crop']
            ? static::crop($source, $width, $height, $params)
            : static::resize($source, $width, $height, $params);
    }
}