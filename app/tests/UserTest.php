<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    protected $user;
    protected $url;

    protected function send_post($url, $data = false)
    {
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        if ($data) {
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $results = curl_exec($ch);//运行curl
        curl_close($ch);

        return json_decode($results);
    }

    public function setUp(): void
    {
        $this->user = new User('');
        $this->url = constant('url');
    }

    public function testLoginSuccess(): string
    {
        $this->url = constant('url') . 'login';
        $data = $this->send_post($this->url, ["last_name" => "Wharlton", 'registration_code' => '52425V']);

        $this->assertSame('Wharlton', $data->lastname);
        $keys = ['firstname', 'lastname', 'username', 'email', 'login_token'];
        foreach ($keys as $key) {
            $this->assertObjectHasAttribute($key, $data);
        }

        return $data->login_token;
    }

    public function testLoginFailed(): void
    {
        $this->url = constant('url') . 'login';
        $data = $this->send_post($this->url, ["last_name" => "Wharlton", 'registration_code' => 'admin']);
        $this->assertSame("Invalid login", $data->message);
    }


    /**
     * @depends  testLoginSuccess
     * */
    public function testLogoutSuccess(string $token): string
    {
        $this->url = constant('url') . 'logout?token=' . $token;
        $result = $this->send_post($this->url);
        $this->assertSame("logout success", $result->message);
        return $token;
    }

    /**
     * @depends  testLogoutSuccess
     * */
    public function testLogoutFailed(string $token): void
    {
        $this->url = constant('url') . 'logout?token=123';
        $result = $this->send_post($this->url);
        $this->assertSame("Invalid token", $result->message);
    }
}

