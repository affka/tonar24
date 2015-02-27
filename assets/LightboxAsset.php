<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class LightboxAsset extends AssetBundle
{
    public $sourcePath = '@bower';
    public $baseUrl = '@web';
    public $css = [
        'lightbox2/css/lightbox.css'
    ];
    public $js = [
        'lightbox2/js/lightbox.js'
    ];
    public $depends = [
    ];
}
