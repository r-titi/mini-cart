<?php

namespace common\models\data;

class ProductData {
    public $id;
    public $user_id;
    public $name;
    public $type;
    public $price;
    public $qty;
    public $category_id;
    public $image;

    public function load(array $data) {
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
    }
}