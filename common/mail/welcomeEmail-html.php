<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
?>
<div>
    <p>Hello <?= Html::encode($username) ?>,</p>

    <p>Welcome <?= $username; ?> in our site, have a good time:</p>

    <p><?= Html::a('http://csp-front.test', 'http://csp-front.test') ?></p>

</div>
