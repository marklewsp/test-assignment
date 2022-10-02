<?php

use yii\db\Migration;

/**
 * Class m221002_143709_total_records
 */
class m221002_143709_total_records extends Migration
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

        $this->createTable('{{%records}}', [
            'id' => $this->primaryKey(),
            'period_start' => $this->date()->notNull(),
            'period_end' => $this->date()->notNull(),
            'message_number' => $this->integer(),
        ], $tableOptions);
    }
}
