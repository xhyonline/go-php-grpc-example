<?php


namespace User;


class UserClient extends \Grpc\BaseStub
{
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }
    public function AddUser(\User\User $argument,$metadata=[],$options=[]){
        // user.UserService/AddUser 是请求方法,格式: 包名.服务名(service)/服务中的方法名
        return $this->_simpleRequest('user.UserService/AddUser',
            $argument,
            // \User\User 是返回参数
            ['\User\User', 'decode'],
            $metadata, $options);
    }

    public function SayHello(\Basic\PBEmpty $argument,$metadata=[],$options=[]){
        // user.UserService/SayHello 调用了proto 中 user包下的UserService 中的 SayHello 方法
        return $this->_simpleRequest('user.UserService/SayHello',
            $argument,
            // \User\User 是返回参数
            ['\Basic\PBEmpty', 'decode'],
            $metadata, $options);
    }
}