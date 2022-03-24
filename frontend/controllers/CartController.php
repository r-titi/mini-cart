<?php

namespace frontend\controllers;

use common\models\Cart;
use common\models\Product;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

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
        // Yii::$app->Cart->getInstance()->clear();
        if(Cart::deleteAll('user_id = ' . Yii::$app->user->id) > 0) {
            return $this->redirect(['cart/index']);
        }
    }

    public function actionAdd($model_id) {
        // Yii::$app->Cart->getInstance()->add($model_id, 1);

        $product = Product::findOne(['id' => $model_id]);
        
        if($product == null)
            throw new NotFoundHttpException('This product does not exist.');
        
        $model = Cart::findOne(['product_id' => $product->id, 'user_id' => Yii::$app->user->id]);

        if($model === null) {
            $model = new Cart;
        }
        
        $model->product_id = $product->id;
        $model->qty = $this->request->post('qty');
        if($model->save()) {
            return $this->redirect(['cart/index']);
        }        
    }
}