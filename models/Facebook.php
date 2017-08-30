<?php

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;


Class Facebook
{

    const USERAGENT = 'Mozilla/5.0 (Series40; Nokia2055/03.20; Profile/MIDP-2.1 Configuration/CLDC-1.1)
     Gecko/20100401 S40OviBrowser/3.9.0.0.22';

    public $client;

    public function __construct() // Создаем иметацию
    {
        $this->client = new Client(
            [
                'base_uri' => 'https://m.facebook.com',
                'User-Agent' => self::USERAGENT,
                'cookies' => true,
                'allow_redirects' => false
            ]
        );
    }

    private function dom($html) // Создаем экземпляр DOM'a
    {
        $dom = new Dom();
        $dom->loadStr($html, []);

        return $dom;
    }

    public function auth($email, $password) // Парсинг формы и авторизация
    {
        $response = $this->client->request('GET', '/'); //
        $loginPageDom = $this->dom($response->getBody());
        $bodyOptions = [];

        foreach ($loginPageDom->find('form input') as $field) { // Перебор input'ов на странице
            $nameAttr = $field->getAttribute('name');
            $valueAttr = $field->getAttribute('value');

            $bodyOptions[$nameAttr] = $valueAttr;
        }
        $bodyOptions['email'] = $email;
        $bodyOptions['pass'] = $password;
        $response = $this->client->post('/login.php', ['form_params' => $bodyOptions]);
        $responseHeaders = $response->getHeaders();
        if (array_key_exists('Location', $responseHeaders)) {
            $location = $responseHeaders['Location'][0];

            if (!stripos($location, '/login/?email')) {
                $this->client->get('/a/language.php?l=en_US', ['allow_redirects' => true]);
                $this->prepareInformation();
                return $this;
            }
        }
    throw new \Exception('Ошибка Авторизации');
    }
    private function prepareInformation(){ // Получения страницы друзей
        $response = $this->client->get('/profile.php?v=friends', ['allow_redirects' => true]);

    }
    private function friendsWithPpk($ppk) // Поиск нужного html тэга
    {
      // $response = $this->client->get('/dmitrynr/friends?lst=100021801792492%3A1661004645%3A1504041548', ['allow_redirects' => true]);

        $response = $this->client->get('/friends/center/friends/?ppk='.$ppk.'&bph='.$ppk, ['allow_redirects' => true]);
     //   echo $this->dom($response->getBody());
        $dom = $this->dom($response->getBody())->find('div.w');
        return count($dom) > 0 ? $dom : false;
    }
    public function _printArray($arr) // Распечатка массива
    {
        echo '<pre>';
        print_r($arr);
        echo '</pre>';
    }
    public function parseFriends() //Парсер друзей по html тэгу
    {
        $friends = [];
        $ppk = 0;
        while ($dom = $this->friendsWithPpk($ppk)) {
            foreach($dom as $friend) {

                parse_str(parse_url($friend->find('.bp')->getAttribute('href'))['query'], $qs);

                $friends[$friend->find('.bp')->text] = $qs['uid'];

            }
            $ppk++;
        }
        foreach ($friends as $key => $friend) {
            if ($works = $this->parseJobs($friend))
                $friends[$key] = $works;
        }
       // $this->_printArray($friends);
        return $friends;
    }
    public function parseJobs($id) //Парсер блока работы по html тэгу
    {
        $works = [];
        $response = $this->client->get('/profile.php?v=info&id='.$id, ['allow_redirects' => true]);
        $dom = $this->dom($response->getBody())->find('#work img');
        foreach ($dom as $work) {
            array_push($works, $work->getAttribute('alt'));
        }

        return count($works) ? $works : false;
    }
}