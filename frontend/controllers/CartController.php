<?php

namespace frontend\controllers;

use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class CartController extends Controller {

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
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }
    
    public function actionIndex() {
        $cart = Yii::$app->Cart->getInstance();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $cart->getAll(),
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

        $cart_total = $cart->getTotal();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'cart_total' => $cart_total
        ]);
    }

    public function actionClearCart() {
        $cart = Yii::$app->Cart->getInstance();
        $cart->clear();
        return $this->redirect(['cart/index']);
    }

    public function actionAdd($model_id) {
        $cart = Yii::$app->Cart->getInstance();
        $cart->add($model_id, 1);
        return $this->redirect(['cart/index']);
    }
}