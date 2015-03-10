<?php

namespace app\models;

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
        file_put_contents($this->getPath(), file_get_contents($this->remoteUrl));
    }

    protected function removeFiles() {
        unlink($this->getDirectoryPath());
    }

}