<?php declare(strict_types=1);

use app\helper\BaseTest;

class UserTest extends BaseTest
{
    protected $user;
    protected $url;

    public function setUp(): void
    {
        $this->user = new User('');
        $this->url = constant('url') . 'login';
    }

    public function testLoginSuccess(): void
    {
        // test attendee 1
        $resAttendee1 = $this->post($this->url, [
            'lastname' => 'Yakovich',
            'registration_code' => '35DGZX',
        ]);

        $this->assertEquals([
            'firstname' => 'Horacio',
            'lastname' => 'Yakovich',
            'username' => 'attendee1',
            'email' => 'hyakovich0@va.gov',
            'token' => $this->LOGIN_TOKEN['attendee1'],
        ], $resAttendee1);


        // test attendee 2
        $resAttendee2 = $this->post($this->url, [
            'lastname' => 'Darthe',
            'registration_code' => 'UP243M',
        ]);

        $this->assertEquals([
            'firstname' => 'Nanon',
            'lastname' => 'Darthe',
            'username' => 'attendee2',
            'email' => 'ndarthe1@list-manage.com',
            'token' => $this->LOGIN_TOKEN['attendee2'],
        ], $resAttendee2);
    }

    public function testB3aSameLastname()
    {
        // test first attendee with lastname Penton
        $resAttendee1 = $this->post($this->url, [
            'lastname' => 'Penton',
            'registration_code' => '9CY9AR',
        ]);

        $this->assertEquals([
            'firstname' => 'Cal',
            'lastname' => 'Penton',
            'username' => 'cpenton6',
            'email' => 'cpenton6@weibo.com',
            'token' => 'a110f35ba8fd08e07de8b275cf186b4f',
        ], $resAttendee1);

        // test second attendee with lastname Penton
        $resAttendee2 = $this->post($this->url, [
            'lastname' => 'Penton',
            'registration_code' => '7BDK38',
        ]);

        $this->assertEquals([
            'firstname' => 'Corbet',
            'lastname' => 'Penton',
            'username' => 'cleamon7',
            'email' => 'cleamon7@pen.io',
            'token' => '6844cd4abd0e90e287b5f138d02eda67',
        ], $resAttendee2);
    }

    public function testB3aSameRegistrationCode()
    {
        // test first attendee with registration code 36PQWG
        $resAttendee1 = $this->post($this->url, [
            'lastname' => 'Arnson',
            'registration_code' => '36PQWG',
        ]);

        $this->assertEquals([
            'firstname' => 'Averil',
            'lastname' => 'Arnson',
            'username' => 'aarnsona',
            'email' => 'aarnsona@princeton.edu',
            'token' => '08ec7801fb781afadd1a9fcccd6d1769',
        ], $resAttendee1);

        // test second attendee with registration code 36PQWG
        $resAttendee2 = $this->post($this->url, [
            'lastname' => 'Dunk',
            'registration_code' => '36PQWG',
        ]);

        $this->assertEquals([
            'firstname' => 'Albertina',
            'lastname' => 'Dunk',
            'username' => 'adunkb',
            'email' => 'adunkb@ifeng.com',
            'token' => 'a39f449906c73a0f218501d91c3c9ee7',
        ], $resAttendee2);
    }

    public function testB3aInvalidLastname()
    {
        $res = $this->post($this->url, [
            'lastname' => 'Yakovichwrong',
            'registration_code' => '35DGZX',
        ]);

        $this->assertEquals([
            'message' => 'Invalid login',
        ], $res);
    }

    public function testB3aInvalidRegistrationCode()
    {
        $res = $this->post($this->url, [
            'lastname' => 'Yakovich',
            'registration_code' => 'AAAAAA',
        ]);

        $this->assertEquals([
            'message' => 'Invalid login',
        ], $res);
    }

    public function testB3aInvalidRequest()
    {
        $res = $this->post($this->url, [
            'foo' => 'bar',
        ]);

        $this->assertEquals([
            'message' => 'Invalid login',
        ], $res);
    }

}

