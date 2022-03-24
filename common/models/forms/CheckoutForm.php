<?php

namespace common\models\forms;

use common\behaviors\CartTotalBehavior;
use common\models\Cart;
use common\models\Order;
use common\models\OrderItem;
use common\models\Product;
use common\models\Shipping;
use Yii;
use yii\base\Model;

class CheckoutForm extends Model {
    
    public $total;
    public $payment_method = 'cod';
    public $status = 'pending';
    public $first_name;
    public $last_name;
    public $address;
    public $order = null;
    public $shipping = null;
    public $currnt_order_item = null;
    public $sellers = [];

    public function behaviors()
    {
        return [
            'carttotal' => [
                'class' => CartTotalBehavior::className(),
                'attributes' => [
                    Model::EVENT_BEFORE_VALIDATE => 'total'
                ],
                'value' => function() {
                    return Cart::find()->where(['user_id' => Yii::$app->user->id])->sum('total');
                }
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['total', 'payment_method', 'status', 'first_name', 'last_name', 'address'], 'required'],
            ['payment_method', 'validatePaymentMethod'],
        ];
    }

    public function validatePaymentMethod($attribute, $params)
    {
        if (!$this->hasErrors()) {
            if (!in_array($this->payment_method, ['cod', 'paypal'])) {
                $this->addError($attribute, 'payment method must be one of this cod or paypal.');
            }
        }
    }

    public function saveOrder() {
        $order = new Order();
        $order->total = $this->total;
        $order->payment_method = $this->payment_method;
        $order->status = $this->status;

        $this->order = $order;
        return $order->save();
    }

    public function saveShipping() {
        $shipping = new Shipping();
        $shipping->first_name = $this->first_name;
        $shipping->last_name = $this->last_name;
        $shipping->address = $this->address;
        $shipping->order_id = $this->order->id;

        $this->shipping = $shipping;
        return $shipping->save();
    }

    public function getAllErrors()
    {
        $errors = [];
        if($this->order != null)
            $errors[] = $this->order->getErrors();

        if($this->shipping != null)
            $errors[] = $this->shipping->getErrors();

        if($this->currnt_order_item != null)
            $errors[] = $this->currnt_order_item->getErrors();

        return $errors;
    }

    public function getFullName() {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function saveOrderItems($items) {
        foreach($items as $cart_item) {
            $orderItem = new OrderItem();
            $orderItem->qty = $cart_item['qty'];
            $orderItem->order_id = $this->order->id;
            $orderItem->product_id = $cart_item['product_id'];

            $p = Product::findOne($cart_item['product_id']);
            $this->sellers[$p->user->email]['email'] = $p->user->email;
            $this->sellers[$p->user->email]['name'] = $p->user->username;
            $this->sellers[$p->user->email]['items'][]['total'] = $p->price * $cart_item['qty'];
            
            $this->currnt_order_item = $orderItem;
            if(!$orderItem->save()) {
                return false;
            }
        }

        return true;
    }
}