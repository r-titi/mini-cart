<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Congratulations!</h1>
    </div>

    <div class="body-content">

        <div class="row">
            <?php foreach($products as $product) {
                ?>
                <div class="col-lg-4 mt-4 mb-4">
                    <img src="<?= 'http://csp-storage.test/uploads/' . $product->image ?>" style="width:100px;height:100px;" alt="">
                    <h2><?= $product->name; ?></h2>
                    <span class=""><?= $product->price; ?> JOD</span>
                    <br>
                    <span class="">Category: <?= $product->category->name; ?></span>
                </div>
            <?php
            } ?>
        </div>

    </div>
</div>
