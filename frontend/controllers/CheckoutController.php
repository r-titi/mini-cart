<?php

namespace frontend\controllers;

use Exception;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use common\models\Cart;
use common\models\forms\CheckoutForm;
use common\models\User;
use common\traits\EmailHelper;

class CheckoutController extends Controller {

    use EmailHelper;
    
    const EVENT_SUBMIT_ORDER = 'submit-order';

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
        $cartItems = Cart::findAll(['user_id' => Yii::$app->user->id]);
        if(count($cartItems) <= 0) {
            echo 'No items in the cart to checkout';
            die();
        }
        
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $checkoutForm = new CheckoutForm();
            $checkoutForm->first_name = $this->request->post('first_name');
            $checkoutForm->last_name = $this->request->post('last_name');
            $checkoutForm->address = $this->request->post('address');

            if(!$checkoutForm->validate()) {
                return $this->sendResponse('Cannot submit order!', $checkoutForm->getErrors(), 400);
            }

            if(!$checkoutForm->saveOrder() || !$checkoutForm->saveShipping() || !$checkoutForm->saveOrderItems($cartItems)) {
                $transaction->rollBack();
                return $this->sendResponse('Cannot submit order!', $checkoutForm->getAllErrors(), 400);
            }

            //send email notification for order owner
            if(!$this->sendEmail([
                'name' => $checkoutForm->getFullName(), 
                'email' => User::findOne(['id' => $checkoutForm->order->user_id])->email,
                'subject' => 'Order Placed Successfully',
                'view' => 'orderUserEmail-html',
                'params' => ['name' => $checkoutForm->getFullName(), 'address' => $checkoutForm->address, 'order' => $checkoutForm->order]
            ])) {
                Yii::warning('Cannot Send Email For ' . User::findOne(['id' => $checkoutForm->order->user_id])->email, 'api.controllers.v1.SiteController.actionCheckout');
            }

            //send notification for all products owners
            foreach($checkoutForm->sellers as $seller) {
                $order_items_amount = 0;
                //calculate requested items total price
                foreach($seller['items'] as $item) {
                    $order_items_amount += $item['total'];
                }
                
                //fire notification event
                $this->trigger(self::EVENT_SUBMIT_ORDER, $this->getOrderEvent($checkoutForm->order, $checkoutForm->shipping, $order_items_amount, User::findByEmail($seller['email'])));
            }

            $transaction->commit();
            return $this->redirect(['site/index']);
        } catch(Exception $e) {
            $transaction->rollBack();
        }
        // $order = new Order();
        // $transaction = Yii::$app->db->beginTransaction();
        // try {
        //     $order->user_id = Yii::$app->user->id;
        //     $order->total = Cart::find()->where(['user_id' => Yii::$app->user->id])->sum('total');;
        //     $order->status = 'pending';
        //     $order->payment_method = 'cod';
        //     $order->created_at = date('U');
            
        //     if(!$order->save()) {
        //         $transaction->rollBack();
        //         print_r($order->getErrors());
        //         exit;
        //     }

        //     $shipping = new Shipping();
        //     $shipping->first_name = $this->request->post('first_name');
        //     $shipping->last_name = $this->request->post('last_name');
        //     $shipping->address = $this->request->post('address');
        //     $shipping->order_id = $order->id;

        //     if(!$shipping->save()) {
        //         $transaction->rollBack();
        //         print_r($shipping->getErrors());
        //         exit;
        //     }

        //     $sellers = [];
        //     foreach($cartItems as $cart_item) {
        //         $orderItem = new OrderItem();
        //         $orderItem->qty = $cart_item['qty'];
        //         $orderItem->order_id = $order->id;
        //         $orderItem->product_id = $cart_item['product_id'];
        //         $orderItem->created_at = date('U');
        //         $p = Product::findOne($cart_item['product_id']);
        //         $sellers[$p->user->email]['email'] = $p->user->email;
        //         $sellers[$p->user->email]['name'] = $p->user->username;
        //         $sellers[$p->user->email]['items'][]['total'] = $p->price * $cart_item['qty'];
                
        //         if(!$orderItem->save()) {
        //             $transaction->rollBack();
        //             print_r($orderItem->getErrors());
        //             exit;
        //         }
        //     }

        //     // clear cart
        //     // Cart::deleteAll('user_id = :user_id', array(':user_id' => $order->user_id));

        //     $this->sendUserOrderEmail(User::findOne(['id' => $order->user_id])->email, $order, $shipping);

        //     foreach($sellers as $seller) {
        //         $order_items_amount = 0;
        //         foreach($seller['items'] as $item) {
        //             $order_items_amount += $item['total'];
        //         }
                
        //         $user = User::findByEmail($seller['email']);

        //         $notificationHelper = new NotificationHelper(['mail', 'database']);
        //         $data = [
        //             'subject' => 'New Order',
        //             'body' => $shipping->first_name . ' ' . $shipping->last_name . ' has been submited new order',
        //             'order_items_amount' => $order_items_amount,
        //             'shipping' => $shipping
        //         ];

        //         $notification = new OrderNotification($data, $order);
        //         $notificationHelper->send([$user], $notification);
            
        //         $data['message'] = 'New Order';
        //         $data['body'] = $shipping->first_name . ' ' . $shipping->last_name . ' has been submited new order';
        //         $pusherHelper = new PusherHelper();
        //         $pusherHelper->trigger('seller-' . $user->id, 'my-event', $data);
        //     }

        //     $transaction->commit();
        //     return $this->sendResponse('Order submitted successfully!', $order, 201);
        // } catch(Exception $e) {
        //     $transaction->rollBack();
        //     return $this->sendResponse('Cannot submit order!', $e->getMessage(), 500);
        // }
    }
}