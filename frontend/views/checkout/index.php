<?php

/** @var yii\web\View $this */

use common\models\Product;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Checkout!</h1>
        <p><?= Yii::$app->session->getFlash('success'); ?></p>
        <p><?= Yii::$app->session->getFlash('error'); ?></p>
    </div>

    <div class="body-content">

        <div class="row" style="margin: 20px;">
            Order Total: <?= $cart_total ?> JD <br>
            By User: <br>
            Date: <br> <?= date('d-m-y'); ?>
            Items:
            <br>
        </div>
        <div class="row">
        <?php
            foreach($cart_items as $item) {
                $product = Product::findOne($item['id']);
             ?>
             <div class="col-lg-3">
                <img src="<?= Yii::getAlias('@common/uploads') . '/' .$product->image; ?>" style="height:100px;width:100px;" alt="sasa">
                <h5><?= $product->name; ?></h5>
                <span class="">Qty <?= $item['qty'] ?> JD</span> <br>
                <span class="">Price <?= $product->price ?> JD</span> <br>
                <span class="">Total <?= $product->price * $item['qty'] ?> JD</span>
            </div>
             <?php   
            }
            ?>
        </div>
        
        <div class="product-form">

            <?php $form = ActiveForm::begin(['action' =>['checkout/submit-order'], 'method' => 'post']); ?>
            <div class="form-group">
                <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
                <?= Html::submitButton('Submit Order', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
