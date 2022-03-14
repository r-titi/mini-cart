<?php

namespace common\components;

use yii\base\Component;
use Exception;
use frontend\helpers\Cart;
use yii\helpers\VarDumper;

class CartComponent extends Component
{

    private Cart $cart;

    public function init()
    {
        parent::init();
    }

    public function __construct()
    {
        $this->cart = new Cart;
    }

    public function getInstance() {
        return $this->cart;
    }
}