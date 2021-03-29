<?php declare(strict_types=1);

use app\helper\BaseTest;

class EventTest extends BaseTest
{
    protected $url;

    public function setUp(): void
    {
        $this->user = new User('');
        $this->url = constant('url') . 'events';
    }

    public function testEventsOverview()
    {
        $data = $this->get($this->url);

        $this->assertEquals([
            'events' => [
                [
                    'id' => 1,
                    'name' => 'WorldSkills Conference 2019',
                    'slug' => 'wsc-2019',
                    'date' => '2019-09-23',
                    'organizer' => ['id' => 1, 'name' => 'Organizerdemo1', 'slug' => 'demo1'],
                ],
                [
                    'id' => 5,
                    'name' => 'ng conf',
                    'slug' => 'ng-2019',
                    'date' => '2019-09-30',
                    'organizer' => ['id' => 2, 'name' => 'Organizerdemo2', 'slug' => 'demo2'],
                ],
                [
                    'id' => 3,
                    'name' => 'React Conf 2019',
                    'slug' => 'react-conf-2019',
                    'date' => '2019-10-24',
                    'organizer' => ['id' => 1, 'name' => 'Organizerdemo1', 'slug' => 'demo1'],
                ],
                [
                    'id' => 4,
                    'name' => 'Vuejs Amsterdam',
                    'slug' => 'vuejs-2019',
                    'date' => '2020-02-14',
                    'organizer' => ['id' => 2, 'name' => 'Organizerdemo2', 'slug' => 'demo2'],
                ],
            ],
        ], $data);
    }
}

