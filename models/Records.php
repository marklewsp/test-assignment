<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "messages".
 *
 * @property int $id
 * @property int $sender_id
 * @property int $thread_id
 * @property string $message
 * @property int $status 0: delivered, 1: seen
 * @property int|null $is_deleted 0: normal, 1: deleted
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $sender
 * @property Threads $thread
 */
class Records extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'records';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message_number'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'period_start' => 'Period Start',
            'period_end' => 'Period End',
            'message_number' => 'Message Number',
        ];
    }

    public static function getTotalRecords()
    {

        $records = Records::find()
                    ->select('*')
                    ->createCommand()
                    ->queryAll();
        return $records;
    }
}

