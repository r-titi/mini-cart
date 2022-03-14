<?php

namespace seller\controllers;

use common\models\Product;
use Yii;
use yii\web\Controller;

class CartController extends Controller {
    public function actionIndex() {
        $product = Product::findOne(5);
        $product1 = Product::findOne(7);
        $product2 = Product::findOne(8);
        Yii::$app->session['cart.product_'.$product->id.'.price'] = $product->price;
        Yii::$app->session['cart.product_'.$product1->id.'.price'] = $product->price;
        Yii::$app->session['cart.product_'.$product2->id.'.price'] = $product->price;
        var_dump(Yii::$app->session['cart.product_'.$product->id.'.price']);
    }

    public function actionAdd($id) {
        $product = Product::findOne($id);
        
    }

    public function actionRemove($id) {
       
    }
}