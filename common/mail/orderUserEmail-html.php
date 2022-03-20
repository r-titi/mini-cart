<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
?>
<div>
    <p>Hello <?= Html::encode($name) ?>,</p>

    <p>Your order has been placed successfully, keep on touch!</p>

    <h4>Order Details:</h4>
    <pre>
        Shipping for: <?= $name ?>
        Shipping address: <?= $address ?> <br>
        Payment Method: <?= $order->payment_method ?>
        Order Amount: <?= $order->total; ?> <br>
        Order Status: <?= $order->status; ?>
        Order Date: <?= date('d-m-y h:m', $order->created_at); ?>
    </pre>

    See other details and manage orders on <p><?= Html::a('http://csp-front.test', 'http://csp-front.test') ?></p>

</div>
