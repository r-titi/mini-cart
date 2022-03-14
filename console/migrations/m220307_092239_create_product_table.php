<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product}}`.
 */
class m220307_092239_create_product_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%product}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'type' => "ENUM('car', 'part')",
            'qty' => $this->integer()->defaultValue(0),
            'price' => $this->float()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'category_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('FK_product_user_user_id', '{{%product}}', 'user_id', '{{%user}}', 'id');
        $this->addForeignKey('FK_product_category_category_id', '{{%product}}', 'category_id', '{{%category}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK_product_user_user_id', '{{%product}}');
        $this->dropForeignKey('FK_product_category_category_id', '{{%product}}');
        $this->dropTable('{{%product}}');
    }
}
