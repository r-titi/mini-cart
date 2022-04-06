<?php

namespace frontend\helpers;

use common\models\Product;
use yii\web\NotFoundHttpException;

class Camo {
    
    private $_job;
    private $_title;
    
    public function do_camo() {

    }

    public function getJob() {
        return $this->_job;
    }

    public function getTitle() {
        return $this->_title;
    }
}