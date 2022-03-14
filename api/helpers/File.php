<?php

namespace api\helpers;

use Yii;

class File {
    
    private array $file;

    public function __construct($file = null)
    {
        $this->file = $file;
    }

    public function getInstance() {
        return $this->file;
    }

    public function generateUniqueName() {
        return \Yii::$app->security->generateRandomString() . '.' . $this->getExtension();
    }

    public function getExtension() {
        return substr($this->file['name'], strpos($this->file['name'], '.') + 1);
    }

    public function getTmpName() {

    }

    public function upload($path, $name) {
        $ofile = fopen($this->file['tmp_name'], "r");
        $fp = fopen($path . '/' . $name, "w");
        while($data = fread($ofile, 1024))
            fwrite($fp, $data);
        fclose($fp);
        fclose($ofile);
    }

    public function isImage() {
        if (!in_array($this->getExtension(), ['png', 'jpg', 'jpeg'])) {
            return false;
        }

        return true;
    }
}