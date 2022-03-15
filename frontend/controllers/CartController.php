<?php

namespace frontend\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;

class CartController extends CustomController {

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['index', 'clear-cart', 'add'], //only be applied to
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'clear-cart', 'add'],
                            'roles' => ['@'],
                        ],
                    ],
                ],
            ]
        );
    }
    
    public function actionIndex() {
        $dataProvider = new ArrayDataProvider([
            'allModels' => Yii::$app->Cart->getInstance()->getAll(),
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
            'cart_total' => Yii::$app->Cart->getInstance()->getTotal()
        ]);
    }

    public function actionClearCart() {
        Yii::$app->Cart->getInstance()->clear();
        return $this->redirect(['cart/index']);
    }

    public function actionAdd($model_id) {
        Yii::$app->Cart->getInstance()->add($model_id, 1);
        return $this->redirect(['cart/index']);
    }
}