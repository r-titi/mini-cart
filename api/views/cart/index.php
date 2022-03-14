<?php

use common\models\Product;
use api\cart\Cart;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'My Cart';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p style="clear: both;">
        <a href="<?= Url::to(['checkout/index'], true); ?>" class="btn btn-success float-left">Checkout</a>
        <a href="<?= Url::to(['cart/clear-cart'], true); ?>" class="btn btn-danger float-right">Clear</a>
    </p>

    <br>
    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'name',
                'header' => 'Name',
                'value' => function( $data ) {
                    return Product::findOne($data['id'])->name;
                },
            ],
            'qty',
            ['attribute' => 'price',
                'header' => 'Price',
                'value' => function( $data ) {
                    return Product::findOne($data['id'])->price . ' JD';
                },
            ],
            ['attribute' => 'subtotal',
                'header' => 'Subtotal',
                'value' => function( $data ) {
                    return Product::findOne($data['id'])->price * $data['qty'] . ' JD';
                },
            ],
            // 'class' => ActionColumn::className(),
            // 'template' => '{mybtn}',
            // 'buttons' => [
            //     'mybtn' => function($url, $model, $key) {
            //         return Html::a('remove', ['cart/index']);
            //     }
            // ]
        ],
    ]); ?>

    <p>Total: <?= $cart_total; ?> JD</p>

    <?php Pjax::end(); ?>

</div>
