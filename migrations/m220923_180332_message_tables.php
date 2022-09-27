<?php

use yii\db\Migration;

/**
 * Class m220923_180332_message_tables
 */
class m220923_180332_message_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%threads}}', [
            'id' => $this->primaryKey(),
            'chatter_one_id' => $this->integer()->notNull(),
            'chatter_two_id' => $this->integer()->notNull(),
            'created_at' => $this->dateTime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
        ], $tableOptions);

        $this->createTable('{{%messages}}', [
            'id' => $this->primaryKey(),
            'sender_id' => $this->integer()->notNull(),
            'thread_id' => $this->integer()->notNull(),
            'message' => $this->text()->notNull(),
            'status' => $this->boolean()->notNull()->defaultValue(0)->comment("0: delivered, 1: seen"),
            'is_deleted' => $this->boolean()->defaultValue(0)->comment("0: normal, 1: deleted"),
            'created_at' => $this->dateTime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            'updated_at' => $this->dateTime()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
        ], $tableOptions);

        $this->addForeignKey("chatter_one_id", '{{%threads}}', "chatter_one_id", '{{%user}}', "id");
        $this->addForeignKey("chatter_two_id", '{{%threads}}', "chatter_two_id", '{{%user}}', "id");
        $this->addForeignKey("thread_id", '{{%messages}}', "thread_id", '{{%threads}}', "id");
        $this->addForeignKey("sender_id", '{{%messages}}', "sender_id", '{{%user}}', "id");
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('{{%threads}}');
        $this->dropTable('{{%messages}}');
    }
}
