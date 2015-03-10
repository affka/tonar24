<?php
/**
 * @author Dmitriy Yurchenko <evildev@evildev.ru>
 */

namespace app\components;

use app\models\FileTrait;
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
     * @param FileTrait $model
     * @param       $width
     * @param       $height
     * @param array $params
     *
     * @return string
     * @throws HttpException
     */
    public static function resize($model, $width, $height, $params = [])
    {
        if (!$model || !is_file($model->path)) {
            return '';
        }

        $fileName = 'r' . $width . 'x' . $height . $model->ext;
        $destinationUrl = $model->directoryUrl . '/' . $fileName;
        $destinationPath = $model->directoryPath . '/' . $fileName;

        //  Если такой файл есть, то ничего ресайзить не надо.
        if (is_file($destinationPath)) {
            return $destinationUrl;
        }

        $imagine = new \Imagine\Imagick\Imagine();
        $image = $imagine->open($model->path);

        try {
            $image->getImagick()->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, true);
            $image->save($destinationPath);
        } catch (\RuntimeException $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        }

        return $destinationUrl;
    }

    /**
     * Обрезает изображение.
     *
     * @param FileTrait $model
     * @param integer $width
     * @param integer $height
     * @param array   $params
     *
     * @return string
     * @throws HttpException
     */
    public static function thumbnail($model, $width, $height, $params = [])
    {
        if (!$model || !is_file($model->path)) {
            return '';
        }

        $fileName = 't' . $width . 'x' . $height . $model->ext;
        $destinationUrl = $model->directoryUrl . '/' . $fileName;
        $destinationPath = $model->directoryPath . '/' . $fileName;

        //  Если такой файл есть, то ничего ресайзить не надо.
        if (is_file($destinationPath)) {
            return $destinationUrl;
        }

        $imagine = new \Imagine\Imagick\Imagine();
        $image = $imagine->open($model->path);
        $geometry = $image->getSize();

            $image
                ->thumbnail(new \Imagine\Image\Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND)
                ->save($destinationPath);

        return $destinationUrl;
    }

    /**
     * Вернет html img тег, с сылкой на изображение с новыми размерами.
     *
     * @param FileTrait $model
     * @param integer $width
     * @param integer $height
     * @param array   $params
     *
     * @return string
     */
    public static function img($model, $width, $height, $params = [])
    {
        if (!$model) {
            return '';
        }

        $alt = isset($params['alt']) ? $params['alt'] : '';
        return isset($params['thumbnail']) && $params['thumbnail']
            ? '<img src="' . static::thumbnail($model, $width, $height, $params) . '" alt="' . $alt . '" />'
            : '<img src="' . static::resize($model, $width, $height, $params) . '" alt="' . $alt . '" />';
    }

    /**
     * Вернет сылку на изображение с новыми размерами.
     *
     * @param FileTrait $model
     * @param integer $width
     * @param integer $height
     * @param array   $params
     *
     * @return string
     */
    public static function url($model, $width, $height, $params = [])
    {
        if (!$model) {
            return '';
        }

        return isset($params['thumbnail']) && $params['thumbnail']
            ? static::thumbnail($model, $width, $height, $params)
            : static::resize($model, $width, $height, $params);
    }
}