<?php

namespace frontend\controllers;

use common\models\Order;
use common\models\OrderItem;
use Exception;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class CheckoutController extends Controller {

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::className(),
                    'only' => ['index', 'submit-order'], //only be applied to
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'submit-order'],
                            'roles' => ['@'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'submit-order' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex() {
        $cart = Yii::$app->Cart->getInstance();
        if($cart->count() <= 0) {
            echo 'No Items in cart';
            exit;
        }
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

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'cart_items' => $cart->getAll(),
            'cart_total' => $cart->getTotal()
        ]);
    }

    public function actionSubmitOrder() {
        $order = new Order();
        $cart = Yii::$app->Cart->getInstance();
        if($cart->count() <= 0) {
            echo 'No Items in cart';
            die();
        }

        try {
            $order->user_id = Yii::$app->user->id;
            $order->total = $cart->getTotal();
            $order->status = 'pending';
            if($order->save(false)) {
                foreach($cart->getAll() as $cart_item) {
                    $orderItem = new OrderItem();
                    $orderItem->qty = $cart_item['qty'];
                    $orderItem->order_id = $order->id;
                    $orderItem->product_id = $cart_item['id'];
                    $orderItem->save(false);
                }

                $cart->clear();
                Yii::$app->session->getFlash('success', 'Order submited successfully!');
                return $this->redirect(['site/index']); 
            } else {
                print_r($order->errors);
                exit;
            }    
        } catch(Exception $e) {
            echo $e->getMessage();
            exit;
            Yii::$app->session->getFlash('error', 'Order submited failed!' . $e->getMessage());
            return $this->redirect(['checkout/index']);
        }
        
    }
}