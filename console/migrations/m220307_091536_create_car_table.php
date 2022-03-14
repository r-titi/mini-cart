<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%car}}`.
 */
class m220307_091536_create_car_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->createTable('{{%car}}', [
        //     'id' => $this->primaryKey(),
        //     'name' => $this->string(255)->notNull(),
        //     'qty' => $this->integer()->defaultValue(0),
        //     'price' => $this->float()->notNull(),
        //     'user_id' => $this->integer()->notNull(),
        //     'category_id' => $this->integer()->notNull(),
        // ]);

        // $this->addForeignKey('FK_car_user_user_id', '{{%car}}', 'user_id', '{{%user}}', 'id');
        // $this->addForeignKey('FK_car_category_category_id', '{{%car}}', 'category_id', '{{%category}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->dropForeignKey('FK_car_user_user_id', '{{%car}}');
        // $this->dropForeignKey('FK_car_category_category_id', '{{%car}}');
        $this->dropTable('{{%car}}');
    }
}
