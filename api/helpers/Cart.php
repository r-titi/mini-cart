<?php

namespace api\cart;

use common\models\Product;
use yii\web\NotFoundHttpException;

class Cart {
    
    private $items = array();

    public function __construct()
    {
        if(!isset($_SESSION)) 
        { 
            session_start(); 
        }

        if(!isset($_SESSION['products'])) {
            $_SESSION['products'] = [];
        }
        $this->items = $_SESSION['products'];
    }

    public function count() {
        return count($this->items);
    }

    public function getAll() {
        return $this->items;
    }

    public function getTotal() {
        $total = 0;
        foreach($this->items as $item) {
            $total += $item['qty'] * Product::findOne($item['id'])->price;
        }

        return $total;
    }

    public function clear() {
        // unset($this->items);
        $this->items = [];
        $_SESSION['products'] = [];
    }

    public function add($model_id, $qty) {
        if(!$this->model_exists($model_id)) {
            throw new NotFoundHttpException('No Product with this Id');
        }

        if(!$this->has($model_id)) {
            $_SESSION['products'][$model_id] = ['id' => $model_id, 'qty' => $qty];
        } else {
            return false;
        }
    }

    public function has($model_id) {
        return array_key_exists($model_id, $this->items);
    }

    public function remove($model_id) {
        if($this->has($model_id)) {
            unset($_SESSION['products'][$model_id]);
            unset($this->items[$model_id]);
        } else {
            return false;
        }
    }

    public function model_exists($model_id) {
        $model = Product::findOne($model_id);
        return ($model) ? true : false;
    }
}