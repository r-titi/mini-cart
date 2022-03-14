<?php

function dd($data)
{
    yii\helpers\VarDumper::dump($data, 10, true);
    Yii::$app->end();
}