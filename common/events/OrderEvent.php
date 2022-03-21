<?php

namespace common\events;

use yii\base\Event;
use yii\base\Model;

class OrderEvent extends Event {

    /**
     * @var Model
     */
    private $_order;
    private $_shipping;
    private $_amount;
    private $_user;

    /**
     * @return Model
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * @param Model $order
     */
    public function setOrder(Model $order)
    {
        $this->_order = $order;
    }

    /**
     * @param Model $shipping
     */
    public function setShipping(Model $_shipping)
    {
        $this->_shipping = $_shipping;
    }

    /**
     * @return Model
     */
    public function getShipping()
    {
        return $this->_shipping;
    }

    public function getAmount()
    {
        return $this->_amount;
    }

    public function setAmount($amount)
    {
        $this->_amount = $amount;
    }

    /**
     * @return Model
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @param Model $user
     */
    public function setUser(Model $user)
    {
        $this->_user = $user;
    }

}