<?php

namespace app\commands;

use app\models\Messages;
use app\models\Threads;
use app\models\User;

class DummyController extends \yii\console\Controller
{
    public $users;

    public $messages;

    public function options($actionID)
    {
        return ['users', 'messages'];
    }

    public function optionAliases()
    {
        return ['u' => 'users', 'm' => 'messages'];
    }

    public function actionGenerateData()
    {
        $lastInsertedUserId = 0;
        User::generateUsers($this->users, $lastInsertedUserId);
        $i = 1;
        $senderId = $receiverId = 1;
        if ($lastInsertedUserId == 1) {
            echo "There is only one user! Cannot create thread!";
            return;
        }

        if ($this->messages == 0) {
            return;
        }

        do {
            $i = $senderId == $receiverId ? $i-1 : $i;
            list ($senderId, $receiverId) = [mt_rand(1, $lastInsertedUserId), mt_rand(1, $lastInsertedUserId)];
            if ($senderId == $receiverId)
                continue;
            echo "Sender ID: {$senderId} - Receiver ID: {$receiverId} \n";
            $threadId = Threads::generateThread($senderId, $receiverId);
            echo "Thread ID: {$threadId} \n";
            Messages::generateMessage($threadId, $senderId);
            echo "Message from {$senderId} in thread {$threadId} \n";
        } while (++$i < $this->messages || $senderId == $receiverId);
    }

}
