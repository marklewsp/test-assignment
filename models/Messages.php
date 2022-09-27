<?php

namespace app\models;

use Faker\Provider\DateTime;
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
class Messages extends \yii\db\ActiveRecord
{

    const SCENARIO_TOTAL = 'total';
    const SORT_VALUES = ["DESC" => "SORT_DESC", "ASC" => SORT_ASC];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sender_id', 'thread_id', 'message'], 'required'],
            [['sender_id', 'thread_id', 'status', 'is_deleted'], 'integer'],
            [['message'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['sender_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sender_id' => 'id']],
            [['thread_id'], 'exist', 'skipOnError' => true, 'targetClass' => Threads::className(), 'targetAttribute' => ['thread_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_id' => 'Sender ID',
            'thread_id' => 'Thread ID',
            'message' => 'Message',
            'status' => 'Status',
            'is_deleted' => 'Is Deleted',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        return array_merge($scenarios, [
            self::SCENARIO_TOTAL => ['period_start', 'period_end', 'period_group_unit'],
        ]);
    }

    /**
     * Gets query for [[Sender]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'sender_id']);
    }

    /**
     * Gets query for [[Thread]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getThread()
    {
        return $this->hasOne(Threads::className(), ['id' => 'thread_id']);
    }

    /**
     * @param $threadId
     * @param $senderId
     */
    public static function generateMessage($threadId, $senderId)
    {
        $faker = DateTime::dateTimeBetween('2016-06-01 00:00:00', '2021-05-31 23:59:59');
        $content = "This is a dummy message!";
        $message = new Messages();
        $message->sender_id = $senderId;
        $message->thread_id = $threadId;
        $message->message = $content;
        $message->created_at = $faker->format('Y-m-d H:i:s');
        $message->save();
        if (!isset($message) || empty($message->id)) {
            throw new \Exception("There is an error on saving messages in the thread of an user!");
        }
    }

    /**
     * @param $start
     * @param $end
     * @param $unit
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getMessagesByTime($start, $end, $unit)
    {
        switch ($unit) {
            case "year":
            case "month":
                $messages = Messages::find()
                    ->select([
                        "DATE('{$start}') as period_start",
                        "DATE('{$end}') as period_end",
                        "COUNT(*) as message_number"])
                    ->andWhere([">=", "created_at", $start])
                    ->andWhere(["<=", "created_at", $end])
                    ->createCommand()
                    ->queryOne();
                break;
            default:
                $messages = Messages::find()
                    ->select(
                        [
                            "DATE(created_at) as period_start",
                            "DATE(created_at) as period_end",
                            "COUNT(*) as message_number"])
                    ->andWhere([">=", "created_at", $start])
                    ->andWhere(["<=", "created_at", $end])
                    ->groupBy(["DATE(created_at)"])
                    ->createCommand()
                    ->queryAll();
                break;
        }
        return $messages;
    }
}

