<?php

namespace app\models;

use Yii;
use yii\db\Exception;
use yii\db\Query;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $verification_token
 *
 * @property Messages[] $messages
 * @property Threads[] $threads
 * @property Threads[] $threads0
 */
class User extends \yii\db\ActiveRecord
{

    public static $nameArr = ['John', 'Paula', 'Doe', 'Phillips', 'Maria', 'Angela'];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['created_at', 'updated_at'], 'integer'],
            [['username'], 'string', 'max' => 255],
            [['username'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
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
        return $this->hasMany(Messages::className(), ['sender_id' => 'id']);
    }

    /**
     * Gets query for [[Threads]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getThreads()
    {
        return $this->hasMany(Threads::className(), ['chatter_one_id' => 'id']);
    }

    /**
     * Gets query for [[Threads0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getThreads0()
    {
        return $this->hasMany(Threads::className(), ['chatter_two_id' => 'id']);
    }

    /**
     * @param $number
     * @param bool $returnLastId
     * @return int
     */
    public static function generateUsers($number, &$lastInsertedId = 0, $returnLastId = TRUE)
    {
        try {
            while ($number-- > 0) {
                $user = new User();
                $user->username = uniqid(strtolower(self::$nameArr[array_rand(self::$nameArr, 1)]) . "_");
                $user->save();
            }
            if ($returnLastId) {
                $lastInsertedId = isset($user) ? $user->id : FALSE;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * @param $start
     * @param $end
     * @param int $limit
     * @param string $dir
     * @return array
     * @throws Exception
     */
    public static function getUserActivity($start, $end, $limit = 10, $dir = "DESC")
    {
        $records = (new Query())
            ->select([
                'u.id',
                'u.username',
                'COUNT(`u`.id) AS message_number'])
            ->from('user as u')
            ->join('LEFT JOIN', 'messages as m', 'u.id = m.sender_id')
            ->andWhere([">=", "m.created_at", $start])
            ->andWhere(["<=", "m.created_at", $end])
            ->groupBy('u.id')
            ->limit($limit)
            ->orderBy("message_number {$dir}")
            ->createCommand()
            ->queryAll();
        return $records;
    }
}
