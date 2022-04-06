<?php

namespace frontend\helpers;

use common\models\Product;
use yii\web\NotFoundHttpException;

class Bio {
    private $_desc;

    public function getDesc() {
        return $this->_desc;
    }
}