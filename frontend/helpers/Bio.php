<?php

namespace frontend\helpers;

use common\models\Product;
use yii\web\NotFoundHttpException;

class Bio {
    private $_desc;

    public function getDesc() {
        return $this->_desc;
    }
    
    public function __construct()
    {
        
    }

    public function __destruct()
    {
        
    }
}