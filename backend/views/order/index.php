<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'All Orders';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['attribute' => 'total',
                'header' => 'Total (JD)',
                'value' => function( $data ) {
                    return $data->total;  
                },
            ],
            'status',
            ['attribute' => 'user_id',
                'header' => 'Owner',
                'value' => function( $data ) {
                    return $data->user->username;  
                },
            ],
            ['attribute' => 'created_at',
                'header' => 'Order Date',
                'value' => function( $data ) {
                    return date('d/M/Y', $data->created_at);  
                },
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
