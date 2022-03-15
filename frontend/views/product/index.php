<?php

use common\models\Product;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'All Products';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="product-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'name',
            'type',
            'qty',
            'price',
            ['attribute' => 'category_id',
                'header' => 'Category',
                'value' => function( $data ) {
                    return $data->category->name;  
                },
            ],
            ['attribute' => 'user_id',
                'header' => 'Owner',
                'value' => function( $data ) {
                    return $data->user->username;  
                },
            ],
           
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
