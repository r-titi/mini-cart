<?php

namespace backend\controllers;

use common\models\Order;
use yii\data\ActiveDataProvider;
use yii\web\Controller;

class OrderController extends Controller {
    
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Order::find(),
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