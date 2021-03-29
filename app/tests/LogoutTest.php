<?php declare(strict_types=1);

use app\helper\BaseTest;

class LogoutTest extends BaseTest
{
    protected $url;

    public function setUp(): void
    {
        $this->url = constant('url') . 'logout?token=';
    }

    public function testB3bLogout()
    {
        // login the user and get the token
        $token = $this->LOGIN_TOKEN['attendee1'];

        // logout the user
        $res = $this->get($this->url.$token);
        $this->assertEquals([
            'message' => 'Logout success',
        ], $res);
    }

    public function testB3bInvalidToken()
    {
        $res = $this->get($this->url.'iaminvalid');
        $this->assertEquals([
            'message' => 'Invalid token',
        ], $res);
    }


    public function testB3bAlreadyLoggedOut()
    {
        // login the user and get the token
        $token = $this->LOGIN_TOKEN['attendee2'];

        // logout the user
        $res = $this->get($this->url.$token);

        $this->assertEquals([
            'message' => 'Logout success',
        ], $res);

        // logout the user again
        $resSecond = $this->get($this->url.$token);
        $this->assertEquals([
            'message' => 'Invalid token',
        ], $resSecond);
    }
}

