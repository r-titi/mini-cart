<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
?>
<div>
    <p>Hello <?= Html::encode($name) ?>,</p>

    <p>A new order on your products has been placed successfully!</p>

    <h4>Order Details:</h4>
    <pre>
        Shipping for: <?= $customer_name ?>
        Shipping address: <?= $address ?> <br>
        Payment Method: <?= $order->payment_method ?>
        Your Items Of Order Amount: <?= $order_items_amount; ?> <br>
        Order Date: <?= date('d-m-y h:m', $order->created_at); ?>
    </pre>

    See other details and manage orders on <p><?= Html::a('http://csp-front.test', 'http://csp-front.test') ?></p>

</div>
