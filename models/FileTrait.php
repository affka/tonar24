<?php

namespace app\models;
use yii\base\Exception;

/**
 * This is the model class for table "product_compl_add".
 *
 * @property $url
 * @property $path
 * @property $directoryPath
 * @property $directoryUrl
 */
trait FileTrait {

    public $remoteUrl;

    public static function generateHash($url) {
        return md5($url);
    }

    public function getUrl() {
        return $this->getDirectoryUrl() . '/orig.' . $this->ext;
    }

    public function getPath() {
        return $this->getDirectoryPath() . '/orig.' . $this->ext;
    }

    public function getDirectoryUrl() {
        return '/uploads/' . static::tableName() . '/' . $this->hash;
    }

    public function getDirectoryPath() {
        return \Yii::$app->getBasePath() . '/web/uploads/' . static::tableName() . '/' . $this->hash;
    }

    public function getIconUrl() {
        switch ($this->ext) {
            case 'doc':
            case 'docx':
                $iconName = 'document';
                break;

            case 'xls':
            case 'xlsx':
                $iconName = 'excel';
                break;

            case 'gif':
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'bmp':
            case 'tiff':
            case 'psd':
                $iconName = 'image';
                break;

            case 'zip':
            case 'rar':
            case 'gz':
            case 'tar':
                $iconName = 'archive';
                break;

            default:
                $iconName = $this->ext;
                break;
        }

        $path = '/img/icons-file/' . $iconName . '.png';
        if (file_exists(\Yii::getAlias('@webroot') . $path)) {
            return \Yii::getAlias('@web') . $path;
        }
        return \Yii::getAlias('@web') . '/img/icons-file/default.png';
    }

    protected function saveFiles() {
        // Check url exists
        if (!$this->remoteUrl) {
            return;
        }

        // Check extension
        $this->ext = strtolower(pathinfo($this->remoteUrl, PATHINFO_EXTENSION));
        //if (!in_array($this->ext, ['jpg', 'jpeg', 'png'])) {
        //    return;
        //}

        if (!$this->hash) {
            $this->hash = static::generateHash($this->remoteUrl);
        }

        // Create dir
        $dir = $this->getDirectoryPath();
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Download file
        $content = @file_get_contents($this->remoteUrl);
        if ($content) {
            file_put_contents($this->getPath(), $content);
        } else {
            $this->ext = null;
        }
    }

    protected function removeFiles() {
        if ($this->hash && $this->ext) {
            unlink($this->getDirectoryPath());
        }
    }

}