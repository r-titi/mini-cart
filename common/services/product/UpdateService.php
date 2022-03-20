<?php

namespace common\services\product;

use common\models\Product;

class UpdateService {
    public function update($data) {
        $product = new Product();
        $product->setScenario(Product::SCENARIO_CREATE);
        $product->name        = $data->name;
        $product->type        = $data->type;
        $product->qty         = $data->qty;
        $product->price       = $data->price;
        $product->category_id = $data->category_id;
        $product->image       = $data->image;

        if($product->validate()) {
            $imgUniqueName = uniqid('pro-');
            $product->image->saveAs('@storage/uploads' . '/' . $imgUniqueName . '.' . $product->image->extension);
            $product->image = $imgUniqueName . '.' . $product->image->extension;
            $product->save();
        }

        return $product;
    }
}