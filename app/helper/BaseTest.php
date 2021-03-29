<?php

namespace app\helper;

use PHPUnit\Framework\TestCase;
use WorldSkills\Trade17\Tests\Helper\Http\Response;

class BaseTest extends TestCase
{
    public $LOGIN_TOKEN = [
        'attendee1' => '6fcf38dfc3b9d4c1816cc536efa7dcca',
        'attendee2' => '2a0b056e56f17fdd885d820342b814b2',
    ];

    public $USERS = [
        'attendee1' => ['lastname' => 'Yakovich', 'registration_code' => '35DGZX'],
        'attendee2' => ['lastname' => 'Darthe', 'registration_code' => 'UP243M'],
    ];

    protected function post($url, $data)
    {
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $results = curl_exec($ch);//运行curl
        curl_close($ch);

        return json_decode($results, true);
    }

    protected function get($url)
    {
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        $results = curl_exec($ch);//运行curl
        curl_close($ch);

        return json_decode($results, true);
    }

    public function assertResponseItemAt(string $key, $data, $result)
    {
        $item = $this->getItemAtKey($key, $result);

        $this->assertEquals($data, $item);
    }

    public function getItemAtKey(string $key, $result)
    {
        $path = explode('.', $key);

        return $result[$path[0]][$path[1]];
    }

    public function getLoginToken($user = 'attendee1')
    {
        $res = $this->post(constant('url').'login', $this->USERS[$user]);

        if (isset($res['token'])) {
            return $res['token'];
        }

        return $this->LOGIN_TOKEN[$user];
    }
}
