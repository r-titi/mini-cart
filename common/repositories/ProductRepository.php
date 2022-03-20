<?php

namespace common\repositories;

use common\models\Product;

class ProductRepository {

    public function getAll() {
        return Product::find()->all();
    }

    public function getById(int $id) : Product {
        return Product::findOne(['id' => $id]);
    }
}