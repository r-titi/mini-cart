<?php

namespace seller\controllers;

use common\models\OrderItem;
use Yii;
use yii\data\ArrayDataProvider;

class OrderController extends CustomController {
    
    public function actionIndex() {
        $items = OrderItem::find()->joinWith([
            'product' => function($q) {
                $q->where(['user_id' => Yii::$app->user->id]);
            }
        ])->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $items,
            /*
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
            */
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}