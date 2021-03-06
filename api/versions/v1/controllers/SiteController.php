<?php

namespace api\versions\v1\controllers;

use api\versions\v1\models\CheckoutForm;
use common\events\handlers\OrderHandler;
use common\events\OrderEvent;
use common\helpers\ApiRouteHelper;
use common\models\Cart;
use common\models\Category;
use common\models\Order;
use common\models\Product;
use common\models\Shipping;
use common\models\User;
use common\traits\EmailHelper;
use Exception;
use Yii;
use yii\filters\AccessControl;

class SiteController extends BaseController
{
    use EmailHelper;

    const EVENT_SUBMIT_ORDER = 'submit-order';

    public function init()
    {
        parent::init();

        //bind event
        $this->on(
            self::EVENT_SUBMIT_ORDER,
            [
                new OrderHandler,
                'handleSubmitOrder',
            ]
        );
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator']['only'] = [
            'get-cart', 'add-to-cart', 'remove-from-cart', 'clear-cart', 'checkout'
        ];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['get-cart', 'add-to-cart', 'checkout'],
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['remove-from-cart', 'clear-cart'],
                    'roles' => ['@'],
                    'matchCallback' => function ($rule, $action) {
                        return $this->isModelOwner('cart');
                    }
                ],
                [
                    'allow' => true,
                    'actions' => [
                        'get-products', 'show-product', 'get-categories', 'show-category', 'get-category-products'
                    ],
                    'roles' => ['?'],
                ],
            ],
        ];
        return $behaviors;
    }

    protected function verbs()
    {
        ApiRouteHelper::generateVerbsFromController(__FILE__, ['checkout' => ['POST']]);
    }

     /**
     * Return the OrderEvent
     *
     * @param Order $order the order model object
     * @param Shipping $shipping shipping model
     * @param $order_items_amount order items amount
     * @param User $user user to notify
     *
     * @return OrderEvent
     * @throws \yii\base\InvalidConfigException
     */
    protected function getOrderEvent(Order $order, Shipping $shipping, $order_items_amount, $user)
    {
        return \Yii::createObject([
            'class' => OrderEvent::class,
            'order' => $order,
            'shipping' => $shipping,
            'amount' => $order_items_amount,
            'user' => $user
        ]);
    }

    public function actionGetProducts() {
        return $this->sendResponse('Products reterived successfully', [
            'total' => Product::find()->count(),
            'items' => Product::find()->all()
        ]);
    }

    public function actionShowProduct($id) {
        return $this->sendResponse('Product reterived successfully', $this->findModel($id, self::PRODUCT));
    }

    public function actionGetCategories() {
        return $this->sendResponse('Categories reterived successfully', [
            'total' => Category::find()->count(),
            'items' => Category::find()->all()
        ]);
    }

    public function actionShowCategory($id) {
        return $this->sendResponse('Category reterived successfully', $this->findModel($id, self::CATEGORY));
    }

    public function actionGetCategoryProducts($id) {
        return $this->sendResponse('Category products reterived successfully', [
            'total' => Product::find()->where(['category_id' => $id])->count(),
            'items' => Product::find()->where(['category_id' => $id])->all()
        ]);
    }

    public function actionGetCart() {
        return $this->sendResponse('Cart items Reterived Successfully', [
            'total' => count(Cart::findAll(['user_id' => Yii::$app->user->id])),
            'items' => Cart::findAll(['user_id' => Yii::$app->user->id])
        ]);
    }

    public function actionAddToCart() {
        $product = $this->findModel($this->request->post('product_id'), self::PRODUCT);
        
        $model = Cart::findOne(['product_id' => $product->id, 'user_id' => Yii::$app->user->id]);

        if($model === null) {
            $model = new Cart;
        }
        
        $model->product_id = $product->id;
        $model->qty = $this->request->post('qty');
        if($model->save()) {
            return $this->sendResponse('Item inserted into cart successfully', $model, 201);
        }

        return $this->sendResponse('Cannot insert into cart, validate your input!', $model->getErrors(), 400);
    }

    public function actionRemoveFromCart($id) {
        if ($this->findModel($id, self::CART)->delete() > 0) {
            $this->response->setStatusCode(204);
        }
    }

    public function actionClearCart() {
        if(Cart::deleteAll('user_id = ' . Yii::$app->user->id) > 0) {
            $this->response->setStatusCode(204);
        }
    }

    public function actionCheckout() {

        $cartItems = Cart::findAll(['user_id' => Yii::$app->user->id]);
        if(count($cartItems) <= 0) {
            return $this->sendResponse('no items in the cart to checkout!', null, 400);
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
            return $this->sendResponse('Order submitted successfully!', $checkoutForm->order, 201);

            // $order->user_id = Yii::$app->user->id;
            // $order->total = Cart::find()->where(['user_id' => Yii::$app->user->id])->sum('total');;
            // $order->status = 'pending';
            // $order->payment_method = 'cod';
            // $order->created_at = date('U');
            
            // if(!$order->save()) {
            //     $transaction->rollBack();
            //     return $this->sendResponse('Cannot submit order!', $order->getErrors(), 400);
            // }

            // $shipping = new Shipping();
            // $shipping->first_name = $this->request->post('first_name');
            // $shipping->last_name = $this->request->post('last_name');
            // $shipping->address = $this->request->post('address');
            // $shipping->order_id = $order->id;

            // if(!$shipping->save()) {
            //     $transaction->rollBack();
            //     return $this->sendResponse('Cannot submit order!', $shipping->getErrors(), 400);
            // }

            // $sellers = [];
            // foreach($cartItems as $cart_item) {
            //     $orderItem = new OrderItem();
            //     $orderItem->qty = $cart_item['qty'];
            //     $orderItem->order_id = $order->id;
            //     $orderItem->product_id = $cart_item['product_id'];
            //     $orderItem->created_at = date('U');
            //     $p = Product::findOne($cart_item['product_id']);
            //     $sellers[$p->user->email]['email'] = $p->user->email;
            //     $sellers[$p->user->email]['name'] = $p->user->username;
            //     $sellers[$p->user->email]['items'][]['total'] = $p->price * $cart_item['qty'];
                
            //     if(!$orderItem->save()) {
            //         $transaction->rollBack();
            //         return $this->sendResponse('Cannot submit order!', $orderItem->getErrors(), 400);
            //     }
            // }

            // clear cart
            // Cart::deleteAll('user_id = :user_id', array(':user_id' => $order->user_id));

            // $this->sendUserOrderEmail(User::findOne(['id' => $order->user_id])->email, $order, $shipping);

            // foreach($sellers as $seller) {
            //     $order_items_amount = 0;
            //     foreach($seller['items'] as $item) {
            //         $order_items_amount += $item['total'];
            //     }
                
            //     //fire notification event
            //     $this->trigger(self::EVENT_SUBMIT_ORDER, $this->getOrderEvent($order, $shipping, $order_items_amount, User::findByEmail($seller['email'])));
            // }

            // $transaction->commit();
            // return $this->sendResponse('Order submitted successfully!', $checkoutForm->order, 201);
        } catch(Exception $e) {
            $transaction->rollBack();
            return $this->sendResponse('Cannot submit order!', $e->getMessage(), 500);
        }
    }
}