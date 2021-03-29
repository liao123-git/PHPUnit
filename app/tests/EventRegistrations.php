<?php declare(strict_types=1);

use app\helper\BaseTest;

class EventRegistrations extends BaseTest
{
    protected $url;

    public function setUp(): void
    {
        $this->user = new User('');
        $this->url = constant('url');
    }

    public function testB4bGetRegistrations()
    {
        // login and try again, should be accessible now
        $res = $this->get($this->url . 'registrations?token=' . $this->getLoginToken('attendee2'));

        $this->assertEquals([
            'registrations' => [
                [
                    'event' => [
                        'id' => 1,
                        'name' => 'WorldSkills Conference 2019',
                        'slug' => 'wsc-2019',
                        'date' => '2019-09-23',
                        'organizer' => [
                            'id' => 1,
                            'name' => 'Organizerdemo1',
                            'slug' => 'demo1',
                        ],
                    ],
                    'session_ids' => [6, 11],
                ],
                [
                    'event' => [
                        'id' => 5,
                        'name' => 'ng conf',
                        'slug' => 'ng-2019',
                        'date' => '2019-09-30',
                        'organizer' => [
                            'id' => 2,
                            'name' => 'Organizerdemo2',
                            'slug' => 'demo2',
                        ],
                    ],
                    'session_ids' => [42],
                ],
            ],
        ], $res);
    }

    /**
     * Test if a new registration gets returned too
     */
    public function testB4bNewRegistration()
    {
        // test count after registration
        $resAfter = $this->get($this->url . 'registrations?token=' . $this->getLoginToken('attendee2'));
        $this->assertEquals([
            'registrations' => [
                [
                    'event' => [
                        'id' => 1,
                        'name' => 'WorldSkills Conference 2019',
                        'slug' => 'wsc-2019',
                        'date' => '2019-09-23',
                        'organizer' => [
                            'id' => 1,
                            'name' => 'Organizerdemo1',
                            'slug' => 'demo1',
                        ],
                    ],
                    'session_ids' => [6, 11],
                ],
                [
                    'event' => [
                        'id' => 5,
                        'name' => 'ng conf',
                        'slug' => 'ng-2019',
                        'date' => '2019-09-30',
                        'organizer' => [
                            'id' => 2,
                            'name' => 'Organizerdemo2',
                            'slug' => 'demo2',
                        ],
                    ],
                    'session_ids' => [42],
                ],
                [
                    'event' => [
                        'id' => 3,
                        'name' => 'React Conf 2019',
                        'slug' => 'react-conf-2019',
                        'date' => '2019-10-24',
                        'organizer' => [
                            'id' => 1,
                            'name' => 'Organizerdemo1',
                            'slug' => 'demo1',
                        ],
                    ],
                    'session_ids' => [24],
                ],
            ],
        ], $resAfter);
    }

    /**
     * Test registrations with a loggedout user
     */
    public function testB4bLoggedOut()
    {
        // test with invalid token
        $resInvalid = $this->get($this->url . 'registrations?token=iaminvalid');
        $this->assertEquals([
            'message' => 'User not logged in',
        ], $resInvalid);

        // test without any token
        $resMissing = $this->get($this->url . 'registrations');
        $this->assertEquals([
            'message' => 'User not logged in',
        ], $resMissing);
    }
}

