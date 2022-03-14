<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'product_name',
                'header' => 'Product Name',
                'value' => function( $data ) {
                    return $data->product->name;  
                },
            ],
            ['attribute' => 'price',
                'header' => 'Price',
                'value' => function( $data ) {
                    return $data->product->price;  
                },
            ],
            ['attribute' => 'qty',
                'header' => 'Quantity',
                'value' => function( $data ) {
                    return $data->qty;  
                },
            ],
            ['attribute' => 'amount',
                'header' => 'Amount',
                'value' => function( $data ) {
                    return $data->qty * $data->product->price;  
                },
            ],
            ['attribute' => 'customer',
                'header' => 'Customer',
                'value' => function( $data ) {
                    return $data->order->user->username;  
                },
            ],
            ['attribute' => 'status',
                'header' => 'Status',
                'value' => function( $data ) {
                    return $data->order->status;  
                },
            ],
            ['attribute' => 'order_date',
                'header' => 'Order Date',
                'value' => function( $data ) {
                    return date('d-m-y', $data->order->created_at);  
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
