<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shipping}}`.
 */
class m220314_065729_create_shipping_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%shipping}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string()->notNull(),
            'last_name' => $this->string()->notNull(),
            'address' => $this->string()->notNull(),
            'order_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('FK_shipping_order_order_id', '{{%shipping}}', 'order_id', '{{%order}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK_shipping_order_order_id', '{{%shipping}}');
        $this->dropTable('{{%shipping}}');
    }
}
