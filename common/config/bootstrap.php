<?php
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@seller', dirname(dirname(__DIR__)) . '/seller');
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
// Yii::setAlias('@frontend/uploads', dirname(dirname(__DIR__)) . '/frontend/web/uploads');
Yii::setAlias('@storage/uploads', dirname(dirname(__DIR__)) . '/storage/web/uploads');