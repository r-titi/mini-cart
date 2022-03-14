<?php

namespace common\components;

use Yii;
use yii\helpers\VarDumper;

class Helpers
{
    public static function dd($data)
    {
        VarDumper::dump($data, 10, true);
        Yii::$app->end();
    }
}
