<?php

namespace api\versions\v1\models;

use common\models\Cart;
use common\models\Order;
use common\models\User;
use Yii;
use yii\base\Model;

class CheckoutForm extends Model {
    public $user_id;
    public $total;
    public $payment_method;
    public $status;
    public $first_name;
    public $last_name;
    public $address;
    private $_order;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'total', 'payment_method', 'status', 'first_name', 'last_name', 'address'], 'required'],
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

    public function submit() {
        
    }
}