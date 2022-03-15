<?php

namespace api\resources;

use common\models\Product;
use common\models\User;

class ProductResource extends Product {
    public function fields()
    {
        return ['id', 'name', 'type', 'qty', 'image', 'category_id', 'user_id'];
    }

    public function extraFields()
    {
        return ['user', 'category'];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getCategory()
    {
        return $this->hasOne(CategoryResource::className(), ['id' => 'category_id']);
    }
}