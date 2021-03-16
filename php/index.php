<?php

require_once 'vendor/autoload.php';

// 由于我们在 composer.json 中定义了 autoload  ==> "User\\":"gen/user/",
// 因此它在看到 User 这个命名空间后,会自动去 gen/user 目录下寻找 UserClient 类
$client=new \User\UserClient("127.0.0.1:8080",[
    'credentials' => \Grpc\ChannelCredentials::createInsecure()
]);

//实例化 TestRequest 请求类
$request = new \User\User();
$request->setAge(24);
$request->setName("小陈");
//调用远程服务
$get = $client->AddUser($request)->wait();

list($reply, $status) = $get;

var_dump($reply->getName());

$request=new Basic\PBEmpty();

$client->SayHello($request);




