<?php

namespace app\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "threads".
 *
 * @property int $id
 * @property int $chatter_one_id
 * @property int $chatter_two_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Messages[] $messages
 * @property User $chatterOne
 * @property User $chatterTwo
 */
class Threads extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'threads';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chatter_one_id', 'chatter_two_id'], 'required'],
            [['chatter_one_id', 'chatter_two_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['chatter_one_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['chatter_one_id' => 'id']],
            [['chatter_two_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['chatter_two_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chatter_one_id' => 'Chatter One ID',
            'chatter_two_id' => 'Chatter Two ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Messages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(Messages::className(), ['thread_id' => 'id']);
    }

    /**
     * Gets query for [[ChatterOne]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChatterOne()
    {
        return $this->hasOne(User::className(), ['id' => 'chatter_one_id']);
    }

    /**
     * Gets query for [[ChatterTwo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChatterTwo()
    {
        return $this->hasOne(User::className(), ['id' => 'chatter_two_id']);
    }

    /**
     * @param $chatterOne
     * @param $chatterTwo
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function loadThreadByUsers($chatterOne, $chatterTwo)
    {
        return self::find()
            ->where(['chatter_one_id' => $chatterOne, 'chatter_two_id' => $chatterTwo])
            ->one();
    }

    public static function generateThread($chatterOne, $chatterTwo)
    {
        if ($chatterOne == $chatterTwo)
            return;

        $thread = self::loadThreadByUsers($chatterOne, $chatterTwo);
        if (isset($thread->id)) {
            return $thread->id;
        } else {
            $thread = new Threads();
            $thread->chatter_one_id = $chatterOne;
            $thread->chatter_two_id = $chatterTwo;
            $thread->save();
            if (!isset($thread) || empty($thread->id)) {
                throw new \Exception("There is an error on saving the thread of non-existed users!");
            }
            return $thread->id;
        }
    }
}
