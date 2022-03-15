<?php

namespace api\versions\v1\controllers;

use api\versions\v1\traits\ResponseHelper;
use common\models\Cart;
use common\models\Order;
use common\models\OrderItem;
use common\models\Product;
use common\models\Shipping;
use Exception;
use Yii;
use yii\web\NotFoundHttpException;

class SiteCartController extends CustomActiveController
{
    use ResponseHelper;

    public $modelClass = 'common\models\Cart';
    
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return $behaviors;
    }

    public function actionIndex() {
        return $this->sendResponse(200, 'Cart items Reterived Successfully', [
            'count' => count(Cart::findAll(['user_id' => Yii::$app->user->id])),
            'items' => Cart::findAll(['user_id' => Yii::$app->user->id])
        ]);
    }

    public function actionAdd() {
        $product = $this->findProduct($this->request->post('product_id'));

        if($this->request->post('qty') > $product->qty) {
            return $this->sendResponse(400, 'this qty for this product is not available!', null);
        }
        
        $model = Cart::findOne(['product_id' => $this->request->post('product_id')]);

        if($model === null) {
            $model = new Cart;
        }
        
        $model->user_id = Yii::$app->user->id;
        $model->product_id = $this->request->post('product_id');
        $model->qty = $this->request->post('qty');
        $model->total = $model->qty * $product->price;
        $model->created_at = date('U');
        if($model->save()) {
            return $this->sendResponse(201, 'Item inserted into cart successfully', $model);
        } else {
            return $this->sendResponse(400, 'Cannot insert into cart, validate your input!', $model->getErrors());
        }
    }

    public function actionRemove($id) {
        $model = $this->findModel($id);
        if ($model->delete() > 0) {
            $this->response->setStatusCode(204);
        }
    }

    public function actionClear() {
        if(Cart::deleteAll('user_id = ' . Yii::$app->user->id) > 0) {
            $this->response->setStatusCode(204);
        }
    }

    public function actionCheckout() {
        $cartItems = Cart::findAll(['user_id' => Yii::$app->user->id]);
        if(count($cartItems) <= 0) {
            return $this->sendResponse(400, 'no items in the cart to checkout!', null);
        }

        $order = new Order();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $order->user_id = Yii::$app->user->id;
            $order->total = Cart::find(['user_id' => Yii::$app->user->id])->sum('total');;
            $order->status = 'pending';
            $order->payment_method = 'cod';
            $order->created_at = date('U');
            if($order->save()) {
                $shipping = new Shipping();
                $shipping->first_name = $this->request->post('first_name');
                $shipping->last_name = $this->request->post('last_name');
                $shipping->address = $this->request->post('address');
                $shipping->order_id = $order->id;
                if($shipping->save()) {
                    foreach($cartItems as $cart_item) {
                        $orderItem = new OrderItem();
                        $orderItem->qty = $cart_item['qty'];
                        $orderItem->order_id = $order->id;
                        $orderItem->product_id = $cart_item['id'];
                        $orderItem->save();
                    }
                } else {
                    return $this->sendResponse(400, 'Cannot submit order!', $shipping->getErrors());
                }

                //clear cart
                Cart::deleteAll('user_id = :user_id', array(':user_id' => $order->user_id));

                $transaction->commit();
                return $this->sendResponse(201, 'Order submitted successfully!', $order);
            } else {
                $transaction->rollBack();
                return $this->sendResponse(400, 'Cannot submit order!', $order->getErrors());
            }
        } catch(Exception $e) {
            $transaction->rollBack();
            return $this->sendResponse(500, 'Cannot submit order!', $e->getMessage());
        }
    }

    protected function findProduct($id)
    {
        if (($model = Product::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('This product does not exist.');
    }

    protected function findModel($id)
    {
        if (($model = Cart::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('This Cart does not exist.');
    }
}