<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_item}}`.
 */
class m220307_093317_create_order_item_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%order_item}}', [
            'id' => $this->primaryKey(),
            'qty' => $this->integer()->notNull(),
            'order_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('FK_order_item_order_order_id', '{{%order_item}}', 'order_id', '{{%order}}', 'id');
        $this->addForeignKey('FK_order_item_product_product_id', '{{%order_item}}', 'product_id', '{{%product}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK_order_item_order_order_id', '{{%order_item}}');
        $this->dropForeignKey('FK_order_item_product_product_id', '{{%order_item}}');
        $this->dropTable('{{%order_item}}');
    }
}
