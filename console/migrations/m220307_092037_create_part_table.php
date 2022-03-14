<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%part}}`.
 */
class m220307_092037_create_part_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $this->createTable('{{%part}}', [
        //     'id' => $this->primaryKey(),
        //     'name' => $this->string(255)->notNull(),
        //     'qty' => $this->integer()->defaultValue(0),
        //     'price' => $this->float()->notNull(),
        //     'user_id' => $this->integer()->notNull(),
        //     'category_id' => $this->integer()->notNull(),
        // ]);

        // $this->addForeignKey('FK_part_user_user_id', '{{%part}}', 'user_id', '{{%user}}', 'id');
        // $this->addForeignKey('FK_part_category_category_id', '{{%part}}', 'category_id', '{{%category}}', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // $this->dropForeignKey('FK_part_user_user_id', '{{%part}}');
        // $this->dropForeignKey('FK_part_category_category_id', '{{%part}}');
        $this->dropTable('{{%part}}');
    }
}
