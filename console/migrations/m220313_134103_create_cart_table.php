<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%cart}}`.
 */
class m220313_134103_create_cart_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%cart}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
            'qty' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('FK_cart_user_user_id', '{{%cart}}', 'user_id', '{{%user}}', 'id');
        $this->addForeignKey('FK_cart_product_product_id', '{{%cart}}', 'product_id', '{{%product}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK_cart_user_user_id', '{{%cart}}');
        $this->dropForeignKey('FK_cart_product_product_id', '{{%cart}}');
        $this->dropTable('{{%cart}}');
    }
}
