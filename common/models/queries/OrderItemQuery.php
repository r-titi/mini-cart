<?php

namespace common\models\queries;

/**
 * This is the ActiveQuery class for [[Order]].
 *
 * @see Order
 */
class OrderItemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Order[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Order|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function bySeller($id) {
        return $this->andOnCondition(['product.user_id' => $id]);
    }
}
