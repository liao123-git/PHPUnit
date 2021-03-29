<?php declare(strict_types=1);

use app\helper\BaseTest;

class EventRegistration extends BaseTest
{
    protected $url;

    public function setUp(): void
    {
        $this->user = new User('');
        $this->url = constant('url');
    }

    public function testB4aCorrectRegistration()
    {
        // test registering for two sessions
        $resTwoSessions = $this->post($this->url . 'organizers/demo1/events/wsc-2019/registration?token=' . $this->getLoginToken(), [
            'ticket_id' => '1',
            'session_ids' => '[9, 11]',
        ]);

        $this->assertEquals([
            'message' => 'Registration successful',
        ], $resTwoSessions);

        // test registering only for event, no sessions
        $resNoSessions = $this->post($this->url . 'organizers/demo1/events/react-conf-2018/registration?token=' . $this->getLoginToken(), [
            'ticket_id' => '4',
        ]);

        $this->assertEquals([
            'message' => 'Registration successful',
        ], $resNoSessions);
    }

    public function testB4aLoggedOut()
    {
        // test with invalid token
        $res = $this->post($this->url . 'organizers/demo1/events/wsc-2019/registration?token=iaminvalid', [
            'ticket_id' => '1',
            'session_ids' => '[9, 11]',
        ]);

        $this->assertEquals([
            'message' => 'User not logged in',
        ], $res);
    }

    public function testB4aAlreadyRegistered()
    {
        // first register
        $resFirst = $this->post($this->url . 'organizers/demo1/events/wsc-2019/registration?token=' . $this->getLoginToken(), [
            'ticket_id' => '1',
            'session_ids' => '[9, 11]',
        ]);

        $this->assertEquals([
            'message' => 'User already registered',
        ], $resFirst);

        // second register for the same event
        $resSecond = $this->post($this->url . 'organizers/demo1/events/wsc-2019/registration?token=' . $this->getLoginToken(), [
            'ticket_id' => '1',
            'session_ids' => '[9, 11]',
        ]);

        $this->assertEquals([
            'message' => 'User already registered',
        ], $resSecond);

        // register with a different ticket for the same event
        $resDifferentTicket = $this->post($this->url . 'organizers/demo1/events/wsc-2019/registration?token=' . $this->getLoginToken(), [
            'ticket_id' => '3',
            'session_ids' => '[9, 11]',
        ]);

        $this->assertEquals([
            'message' => 'User already registered',
        ], $resDifferentTicket);
    }

    public function testB4aInvalidTicket()
    {
        // test with a date in the past
        $resDateExpired = $this->post($this->url . 'organizers/demo1/events/wsc-2019/registration?token=' . $this->getLoginToken(), [
            'ticket_id' => '2',
            'session_ids' => '[9, 11]',
        ]);

        $this->assertEquals([
            'message' => 'Ticket is no longer available',
        ], $resDateExpired);

        // do registration for last available tickets (ticket id 7 already has 34 of 35)
        $resLastAvailable = $this->post($this->url . 'organizers/demo1/events/react-conf-2019/registration?token=' . $this->getLoginToken(), [
            'ticket_id' => '7',
            'session_ids' => '[24]',
        ]);

        $this->assertEquals([
            'message' => 'Registration successful',
        ], $resLastAvailable);

        // register for the same event again, should now longer be possible
        $resMaxAmountReached = $this->post($this->url . 'organizers/demo1/events/react-conf-2019/registration?token=' . $this->getLoginToken('attendee2'), [
            'ticket_id' => '7',
            'session_ids' => '[24]',
        ]);

        $this->assertEquals([
            'message' => 'Ticket is no longer available',
        ], $resMaxAmountReached);
    }
}

