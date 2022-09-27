<?php
use app\models\User;
use app\models\Threads;
use app\models\Messages;

class DummyTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    /**
     *
     */
    public function testValidate()
    {
        $user = new User();

        $user->username = null;
        $this->assertFalse($user->validate(['username']));

        $user->username = 'TooooooooooooooooooooooooLongggggggggggggggggggggggNammmmmmmmemmmemememememememmemememememememeTooooooooooooooooooooooooLongggggggggggggggggggggggNammmmmmmmemmmemememememememmemememememememeTooooooooooooooooooooooooLongggggggggggggggggggggggNammmmmmmmemmmemememememememmemememememememe';
        $this->assertFalse($user->validate(['username']));

        $user->username = 'john_asdsa231231asdas';
        $this->assertTrue($user->validate(['username']));
    }

    /**
     *
     */
    public function testGenerateUsers()
    {

        $number = 0;
        $lastInsertId = FALSE;
        User::generateUsers(0, $lastInsertId);
        $this->assertEquals(FALSE, $lastInsertId);

        User::generateUsers(1, $lastInsertId);
        $this->assertIsInt($lastInsertId);
    }

    public function testNotGenerateThread()
    {
        $userOneId = 1;
        $userTwoId = 2;

        $this->expectException(Exception::class);
        Threads::generateThread($userOneId, $userTwoId);
        $this->expectExceptionMessage("There is an error on saving the thread of non-existed users!");
    }

    public function testGenerateThread()
    {
        $lastInsertId = 0;
        $userOne = new User();
        $userOne->username = "user_one";
        $userOne->save();

        $userTwo = new User();
        $userTwo->username = "user_two";
        $userTwo->save();

        $lastInsertId = Threads::generateThread($userOne->id, $userTwo->id);
        $this->assertIsInt($lastInsertId);

    }

    public function testNotGenerateMessage()
    {
        $senderId = 1;
        $threadId = 1;

        $this->expectException(Exception::class);
        Messages::generateMessage($senderId, $threadId);
        $this->expectExceptionMessage("There is an error on saving messages in the thread of an user!");
    }

    public function testGenerateMessage()
    {
        $userOne = new User();
        $userOne->username = "user_one";
        $userOne->save();

        $userTwo = new User();
        $userTwo->username = "user_two";
        $userTwo->save();

        $thread = new Threads();
        $thread->chatter_one_id = $userOne->id;
        $thread->chatter_two_id = $userTwo->id;
        $thread->save();

        $this->expectNotToPerformAssertions(Messages::generateMessage($thread->id, $userOne->id));
    }
}